<?php
declare(strict_types=1);

namespace imissyouso\CsvAggregatorBundle\Writer;

class SimpleCsvWriter implements WriterInterface
{
    /**
     * @var string
     */
    private $outDir;

    /**
     * @var resource
     */
    private $pointer;

    /**
     * @var string
     */
    private $delimiter;

    /**
     * SimpleCsvWriter constructor.
     * @param string $outDir
     * @param string $delimiter
     */
    public function __construct(string $outDir, string $delimiter)
    {
        $this->outDir = $outDir;
        $this->delimiter = $delimiter;
    }

    public function init(): void
    {
        $this->pointer = fopen($this->outDir, 'wb+');
    }

    public function write(string $line): void
    {
        fputcsv($this->pointer, explode($this->delimiter, $line), $this->delimiter);
    }

    public function close(): void
    {
        fclose($this->pointer);
    }
}
