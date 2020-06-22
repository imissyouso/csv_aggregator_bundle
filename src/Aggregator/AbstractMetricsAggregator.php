<?php
declare(strict_types=1);

namespace imissyouso\CsvAggregatorBundle\Aggregator;

use imissyouso\CsvAggregatorBundle\Storage\StorageInterface;
use imissyouso\CsvAggregatorBundle\Writer\WriterInterface;

abstract class AbstractMetricsAggregator
{
    /**
     * @var StorageInterface
     */
    private $storage;

    /**
     * @var WriterInterface
     */
    private $writer;

    /**
     * @var array
     */
    protected $header;

    /**
     * AbstractMetricsAggregator constructor.
     * @param StorageInterface $storage
     * @param WriterInterface $writer
     * @param array $header
     */
    public function __construct(StorageInterface $storage, WriterInterface $writer, array $header)
    {
        $this->storage = $storage;
        $this->writer = $writer;
        $this->header = $header;
    }

    public function run(): void
    {
        $rows = $this->getRows();

        foreach ($rows as $metrics) {
            $this->storage->put($metrics->getRowName(), $metrics->getValues());
        }

        $this->writer->init();

        $this->writer->write($this->rowToString($this->header));

        foreach ($this->storage as $key => $row) {
            array_unshift($row, $key);
            $this->writer->write($this->rowToString($row));
        }

        $this->writer->close();

        $this->storage->drop();
    }

    abstract protected function getRows(): \Iterator;

    abstract protected function rowToString($row): string;
}
