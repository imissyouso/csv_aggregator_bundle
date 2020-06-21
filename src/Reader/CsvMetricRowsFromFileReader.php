<?php
declare(strict_types=1);

namespace imissyouso\CsvAggregatorBundle\Reader;

use imissyouso\CsvAggregatorBundle\Iterator\CsvMetricsRowIterator;
use imissyouso\CsvAggregatorBundle\RowProcessor\MetricsRowProcessorInterface;
use imissyouso\CsvAggregatorBundle\Validator\CsvValidatorInterface;
use SplFileObject;

class CsvMetricRowsFromFileReader implements \IteratorAggregate
{
    /**
     * @var string
     */
    private $filePath;

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
     * @param string $filePath
     * @param string $delimiter
     * @param CsvValidatorInterface $validator
     * @param MetricsRowProcessorInterface $rowProcessor
     */
    public function __construct(string $filePath, string $delimiter, CsvValidatorInterface $validator, MetricsRowProcessorInterface $rowProcessor)
    {
        $this->filePath = $filePath;
        $this->delimiter = $delimiter;
        $this->validator = $validator;
        $this->rowProcessor = $rowProcessor;
    }

    public function getIterator()
    {
        $fileObj = new SplFileObject($this->filePath);

        $fileObj->setFlags(
            SplFileObject::READ_CSV
            | SplFileObject::SKIP_EMPTY
            | SplFileObject::READ_AHEAD
            | SplFileObject::DROP_NEW_LINE
        );

        $fileObj->setCsvControl($this->delimiter);

        return new CsvMetricsRowIterator($fileObj, $this->validator, $this->rowProcessor);
    }
}
