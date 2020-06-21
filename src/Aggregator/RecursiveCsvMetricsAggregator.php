<?php
declare(strict_types=1);

namespace imissyouso\CsvAggregatorBundle\Aggregator;

use imissyouso\CsvAggregatorBundle\RowProcessor\MetricsRowProcessorInterface;
use imissyouso\CsvAggregatorBundle\Storage\StorageInterface;
use imissyouso\CsvAggregatorBundle\Reader\CsvMetricRowsFromFilesReader;
use imissyouso\CsvAggregatorBundle\Validator\StrictDateMetricsCsvValidator;
use imissyouso\CsvAggregatorBundle\Writer\WriterInterface;
use Symfony\Component\Finder\Finder;

class RecursiveCsvMetricsAggregator extends AbstractMetricsAggregator
{
    /**
     * @var string
     */
    private $delimiter;
    /**
     * @var MetricsRowProcessorInterface
     */
    private $rowProcessor;
    /**
     * @var string
     */
    private $directory;

    /**
     * RecursiveCsvMetricsAggregator constructor.
     * @param StorageInterface $storage
     * @param WriterInterface $writer
     * @param string $directory
     * @param array $header
     * @param string $delimiter
     * @param MetricsRowProcessorInterface $rowProcessor
     */
    public function __construct(StorageInterface $storage, WriterInterface $writer, string $directory, array $header, string $delimiter, MetricsRowProcessorInterface $rowProcessor)
    {
        parent::__construct($storage, $writer, $header);
        $this->delimiter = $delimiter;
        $this->rowProcessor = $rowProcessor;
        $this->directory = $directory;
    }

    /**
     * @return \Iterator
     * @throws \Exception
     */
    protected function getRows(): \Iterator
    {
        $finder = new Finder();
        $finder->files()->name('*.csv')->in($this->directory);

        return (new CsvMetricRowsFromFilesReader(
            $finder,
            $this->delimiter,
            new StrictDateMetricsCsvValidator($this->header),
            $this->rowProcessor
        ))->getIterator();
    }

    protected function rowToString($row): string
    {
        return implode($this->delimiter, $row);
    }
}
