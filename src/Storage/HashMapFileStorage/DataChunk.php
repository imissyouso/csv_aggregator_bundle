<?php
declare(strict_types=1);

namespace imissyouso\CsvAggregatorBundle\Storage\HashMapFileStorage;

class DataChunk
{
    /**
     * @var int
     */
    private $offset;

    /**
     * @var int
     */
    private $key;

    /**
     * @var array
     */
    private $values;

    /**
     * @var int
     */
    private $linkToTheNextChunk;

    /**
     * DataChunk constructor.
     * @param int $offset
     * @param int $key
     * @param array $values
     * @param int $linkToTheNextChunk
     */
    public function __construct(int $offset, int $key, array $values, int $linkToTheNextChunk)
    {
        $this->key = $key;
        $this->values = $values;
        $this->linkToTheNextChunk = $linkToTheNextChunk;
        $this->offset = $offset;
    }

    /**
     * @return int
     */
    public function getKey(): int
    {
        return $this->key;
    }

    /**
     * @param int $key
     */
    public function setKey(int $key): void
    {
        $this->key = $key;
    }

    /**
     * @return array
     */
    public function getValues(): array
    {
        return $this->values;
    }

    /**
     * @param array $values
     */
    public function setValues(array $values): void
    {
        $this->values = $values;
    }

    /**
     * @return int
     */
    public function getLinkToTheNextChunk(): int
    {
        return $this->linkToTheNextChunk;
    }

    /**
     * @param int $linkToTheNextChunk
     */
    public function setLinkToTheNextChunk(int $linkToTheNextChunk): void
    {
        $this->linkToTheNextChunk = $linkToTheNextChunk;
    }

    /**
     * @return int
     */
    public function getOffset(): int
    {
        return $this->offset;
    }

    /**
     * @param int $offset
     */
    public function setOffset(int $offset): void
    {
        $this->offset = $offset;
    }
}
