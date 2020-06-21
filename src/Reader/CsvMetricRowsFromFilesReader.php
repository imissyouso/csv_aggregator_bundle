<?php
declare(strict_types=1);

namespace imissyouso\CsvAggregatorBundle\Reader;

use imissyouso\CsvAggregatorBundle\RowProcessor\MetricsRowProcessorInterface;
use imissyouso\CsvAggregatorBundle\Validator\CsvValidatorInterface;

class CsvMetricRowsFromFilesReader implements \IteratorAggregate
{
    /**
     * @var \Iterator
     */
    private $collection;
    /**
     * @var string
     */
    private $delimiter;

    /**
     * @var CsvValidatorInterface
     */
    private $validator;
    /**
     * @var MetricsRowProcessorInterface
     */
    private $rowProcessor;

    /**
     * FileCsvMetricsIterator constructor.
     */
    public function __construct(\Traversable $collection, string $delimiter, CsvValidatorInterface $validator, MetricsRowProcessorInterface $rowProcessor)
    {
        $this->collection = $collection;
        $this->delimiter = $delimiter;
        $this->validator = $validator;
        $this->rowProcessor = $rowProcessor;
    }

    public function getIterator(): \Iterator
    {
        $appendIterator = new \AppendIterator();

        foreach ($this->collection as $file) {
            if (!($file instanceof \SplFileInfo)) {
                continue;
            }

            $csvMetricsRowFileIterator = new CsvMetricRowsFromFileReader($file->getRealPath(), $this->delimiter, $this->validator, $this->rowProcessor);

            $appendIterator->append($csvMetricsRowFileIterator->getIterator());
        }

        return $appendIterator;
    }
}
