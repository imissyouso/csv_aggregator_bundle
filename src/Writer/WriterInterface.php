<?php
declare(strict_types=1);

namespace imissyouso\CsvAggregatorBundle\Writer;

interface WriterInterface
{
    public function init(): void;

    public function write(string $line): void;

    public function close(): void;
}
