<?php
declare(strict_types=1);

namespace imissyouso\CsvAggregatorBundle\HashKeyConverter;

class SimpleHashStringConverter implements HashConverterInterface
{
    public function toInt(string $source): int
    {
        return (int) str_replace('-', '', $source);
    }

    public function toString(int $source): string
    {
        return preg_replace("/([\d]{4})([\d]{2})([\d]{2})/", '$1-$2-$3', (string) $source);
    }
}
