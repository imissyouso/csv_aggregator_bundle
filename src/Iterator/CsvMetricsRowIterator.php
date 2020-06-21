<?php
declare(strict_types=1);

namespace imissyouso\CsvAggregatorBundle\Iterator;

use imissyouso\CsvAggregatorBundle\RowProcessor\MetricRowProcessException;
use imissyouso\CsvAggregatorBundle\RowProcessor\MetricsRowProcessorInterface;
use imissyouso\CsvAggregatorBundle\Validator\CsvValidatorInterface;
use Iterator;

class CsvMetricsRowIterator implements Iterator
{
    /**
     * @var Iterator
     */
    private $baseCsvIterator;

    /**
     * @var CsvValidatorInterface
     */
    private $csvValidator;

    /**
     * @var array
     */
    private $header;

    /**
     * @var MetricsRowProcessorInterface
     */
    private $rowProcessor;

    /**
     * CsvAggregator constructor.
     * @param Iterator $baseCsvIterator
     * @param CsvValidatorInterface $csvValidator
     * @param MetricsRowProcessorInterface $rowProcessor
     */
    public function __construct(
        Iterator $baseCsvIterator,
        CsvValidatorInterface $csvValidator,
        MetricsRowProcessorInterface $rowProcessor
    ) {
        $this->baseCsvIterator = $baseCsvIterator;
        $this->csvValidator = $csvValidator;
        $this->rowProcessor = $rowProcessor;
    }

    public function current()
    {
        if (!$this->header) {
            $this->validateHeader();
        }

        $row = $this->baseCsvIterator->current();

        try {
            return $this->rowProcessor->process($this->header, $row);
        } catch (MetricRowProcessException $e) {
            $this->baseCsvIterator->next();

            return $this->current();
        }
    }

    public function next(): void
    {
        $this->baseCsvIterator->next();
    }

    public function key(): int
    {
        return $this->baseCsvIterator->key() - 1;
    }

    public function valid(): bool
    {
        return $this->baseCsvIterator->valid();
    }

    public function rewind(): void
    {
        $this->baseCsvIterator->rewind();

        // skip header line
        $this->baseCsvIterator->next();
    }

    private function validateHeader(): void
    {
        $this->baseCsvIterator->rewind();

        $this->header = $this->baseCsvIterator->current();

        $this->csvValidator->validateHeader(
            array_map('trim', $this->header)
        );

        $this->baseCsvIterator->next();
    }
}
