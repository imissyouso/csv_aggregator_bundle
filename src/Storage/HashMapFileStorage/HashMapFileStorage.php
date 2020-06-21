<?php
declare(strict_types=1);

namespace imissyouso\CsvAggregatorBundle\Storage\HashMapFileStorage;

use imissyouso\CsvAggregatorBundle\AggregateStrategy\AggregateStrategyInterface;
use imissyouso\CsvAggregatorBundle\HashKeyConverter\HashConverterInterface;
use imissyouso\CsvAggregatorBundle\Storage\StorageInterface;

// @TODO: move IO operations to another class, but do we really need it? in pursuit of 'S' it sounds like the real over engineering.
class HashMapFileStorage implements StorageInterface
{
    /**
     * @var resource
     */
    private $filePointer;

    /**
     * @var int
     */
    private $hashMapBucketSize;

    /**
     * @var float|int
     */
    private $currentTmpFileSize;

    /**
     * @var HashConverterInterface
     */
    private $keyConverter;

    /**
     * @var int
     */
    private $valuesPerRow;

    /**
     * @var AggregateStrategyInterface
     */
    private $aggregateStrategy;

    /**
     * @var bool
     */
    private $init;

    /**
     * @var string
     */
    private $filePath;

    /**
     * @var DataChunk
     */
    private $prevChunk;

    /**
     * HashMapRowAggregator constructor.
     *
     * @param HashConverterInterface $keyConverter
     * @param string $filePath
     * @param AggregateStrategyInterface $aggregateStrategy
     * @param int $hashMapBucketSize
     * @param int $valuesPerRow
     */
    public function __construct(
        HashConverterInterface $keyConverter,
        string $filePath = null,
        AggregateStrategyInterface $aggregateStrategy = null,
        int $hashMapBucketSize = 1024,
        int $valuesPerRow = 3
    ) {
        $this->keyConverter = $keyConverter;
        $this->hashMapBucketSize = $hashMapBucketSize;
        $this->valuesPerRow = $valuesPerRow;
        $this->aggregateStrategy = $aggregateStrategy;
        $this->filePath = $filePath ?? sprintf('/tmp/%s.bin', uniqid('', true));
    }

    public function init(): void
    {
        if ($this->init) {
            return;
        }

        $this->init = true;

        $this->currentTmpFileSize = $this->getChunkSize() * $this->hashMapBucketSize;

        $this->filePointer = fopen($this->filePath, 'wb+');
        // Populate new empty bucket with zeros
        fwrite($this->filePointer, str_repeat("\0", $this->currentTmpFileSize));
    }

    /**
     * @return \Iterator
     * @throws \Exception
     */
    public function all(): \Iterator
    {
        return $this->getIterator();
    }

    public function getIterator(): \Iterator
    {
        if($this->prevChunk){
            $this->writeChunk($this->prevChunk);
            $this->prevChunk = null;
        }

        rewind($this->filePointer);

        while ($currentChunk = fread($this->filePointer, $this->getChunkSize())) {
            $chunk = $this->parseChunk($currentChunk);

            if ($chunk->getKey()) {
                yield $this->keyConverter->toString($chunk->getKey()) => array_map(
                    static function ($v) {
                        return round($v, floor($v) === $v ? 0 : 2);
                    },
                    $chunk->getValues()
                );
            }
        }
    }

    public function drop(): void
    {
        unlink($this->filePath);
    }

    public function setValuesPerRow(int $valuesPerRow): void
    {
        $this->valuesPerRow = $valuesPerRow;
    }

    public function put(string $key, array $values): void
    {
        $this->init();

        $keyHash = $this->keyConverter->toInt($key);
        $bucketOffset = ($keyHash & ($this->hashMapBucketSize - 1)) * $this->getChunkSize();

        $this->save($bucketOffset, $keyHash, $values);
    }

    protected function save(int $offset, int $rowNameHash, array $values): void
    {
        $chunk = $this->tryToReadChunkFromMemory($offset, $rowNameHash);

        // if prev chunk exists and current key is differ then write prev chunk on the disk
        if($this->prevChunk && $this->prevChunk->getKey() !== $rowNameHash){
            $this->writeChunk($this->prevChunk);
        }

        // If the place is already filled and has a link to another place in memory then jump there and try to save again
        if ($chunk->getKey() !== $rowNameHash && $chunk->getLinkToTheNextChunk()) {
            $this->save($chunk->getLinkToTheNextChunk(), $rowNameHash, $values);
            return;
        }

        if (!$chunk->getKey() || $chunk->getKey() === $rowNameHash) {
            // Aggregate values here if aggregator is set
            $chunkValues = $chunk->getValues();
            foreach ($chunkValues as $i => $chunkValue) {
                if ($this->aggregateStrategy) {
                    $values[$i] = $this->aggregateStrategy->apply($chunkValue, $values[$i]);
                }
            }

            $this->prevChunk = new DataChunk($offset, $rowNameHash, $values, 0);

            return;
        }

        // Collision detected! Let's resolve this shit! See https://habr.com/ru/post/421179/
        if ($chunk->getKey() !== $rowNameHash) {
            $this->resolveCollision($offset, $rowNameHash, $values);
        }
    }

    protected function getChunkSize(): int
    {
        // every chunk has ROW_HASH(4b)|...AGGREGATED_VALUES(4b*n)|LINK_TO_THE_COLLISIONAL_CHUNK(4b) structure in sum it gives us 20 bytes
        return 4 + 4 + ($this->valuesPerRow * 4);
    }

    protected function parseChunk(string $chunk): DataChunk
    {
        // every chunk has ROW_HASH|...AGGREGATED_VALUES|LINK_TO_THE_COLLISIONAL_CHUNK structure in sum it gives us 20 bytes
        $parsedRowNameHash = unpack('I', $chunk)[1];
        $parsedValues = array_values(unpack('f'.$this->valuesPerRow, $chunk, 4));
        $parsedLinkToTheNextChunk = unpack('L', $chunk, $this->getChunkSize() - 4)[1];

        return new DataChunk(0, $parsedRowNameHash, $parsedValues, $parsedLinkToTheNextChunk);
    }

    protected function tryToReadChunkFromMemory(int $offset, int $rowNameHash): ?DataChunk
    {
        // If prev chunk is already loaded then get it without file reading
        if($this->prevChunk && $this->prevChunk->getKey() === $rowNameHash){
            return $this->prevChunk;
        }

        return $this->readChunk($offset);
    }

    protected function readChunk(int $offset): DataChunk {
        fseek($this->filePointer, $offset, SEEK_SET);

        $currentChunk = fread($this->filePointer, $this->getChunkSize());

        fseek($this->filePointer, $offset, SEEK_SET);

        $chunk = $this->parseChunk($currentChunk);

        $chunk->setOffset($offset);

        return $chunk;
    }

    protected function writeChunk(DataChunk $chunk): void {
        fseek($this->filePointer, $chunk->getOffset(), SEEK_SET);

        fwrite($this->filePointer, pack('I', $chunk->getKey()), 4);
        fwrite($this->filePointer, pack('f'.$this->valuesPerRow, ...$chunk->getValues()), count($chunk->getValues()) * 4);

        fseek($this->filePointer, $chunk->getOffset(), SEEK_SET);
    }

    protected function resolveCollision(int $offset, int $rowNameHash, array $values): void {
        fseek($this->filePointer, ($offset + $this->getChunkSize()) - 4, SEEK_SET);

        fwrite($this->filePointer, pack('L', $this->currentTmpFileSize), 4);

        fseek($this->filePointer, 0, SEEK_END);

        fwrite($this->filePointer, str_repeat("\0", $this->getChunkSize()));

        $newOffset = $this->currentTmpFileSize;
        $this->currentTmpFileSize += $this->getChunkSize();

        $this->save($newOffset, $rowNameHash, $values);
    }
}
