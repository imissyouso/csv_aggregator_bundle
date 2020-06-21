<?php
declare(strict_types=1);

namespace imissyouso\CsvAggregatorBundle\Factory;

use imissyouso\CsvAggregatorBundle\Aggregator\AbstractMetricsAggregator;
use imissyouso\CsvAggregatorBundle\Aggregator\RecursiveCsvMetricsAggregator;
use imissyouso\CsvAggregatorBundle\RowProcessor\MetricsRowProcessorInterface;
use imissyouso\CsvAggregatorBundle\Storage\StorageInterface;
use imissyouso\CsvAggregatorBundle\Writer\WriterInterface;

class RecursiveCsvMetricsAggregatorFactory implements CsvMetricsAggregatorFactoryInterface
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
     * @var string
     */
    private $sourceDir;
    /**
     * @var array
     */
    private $header;
    /**
     * @var string
     */
    private $delimiter;
    /**
     * @var MetricsRowProcessorInterface
     */
    private $rowProcessor;

    /**
     * RecursiveCsvMetricsAggregatorFactory constructor.
     * @param StorageInterface $storage
     * @param WriterInterface $writer
     * @param string $sourceDir
     * @param array $header
     * @param string $delimiter
     * @param MetricsRowProcessorInterface $rowProcessor
     */
    public function __construct(StorageInterface $storage, WriterInterface $writer, string $sourceDir, array $header, string $delimiter, MetricsRowProcessorInterface $rowProcessor)
    {
        $this->storage = $storage;
        $this->writer = $writer;
        $this->sourceDir = $sourceDir;
        $this->header = $header;
        $this->delimiter = $delimiter;
        $this->rowProcessor = $rowProcessor;
    }

    public function make(): AbstractMetricsAggregator
    {
        $this->storage->setValuesPerRow(count($this->header) - 1);

        return new RecursiveCsvMetricsAggregator(
            $this->storage,
            $this->writer,
            $this->sourceDir,
            $this->header,
            $this->delimiter,
            $this->rowProcessor
        );
    }
}
