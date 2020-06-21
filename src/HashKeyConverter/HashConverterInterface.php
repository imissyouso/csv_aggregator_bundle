<?php
declare(strict_types=1);

namespace imissyouso\CsvAggregatorBundle\HashKeyConverter;

interface HashConverterInterface
{
    public function toInt(string $source): int;

    public function toString(int $source): string;
}
