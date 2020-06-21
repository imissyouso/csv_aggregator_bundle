<?php
declare(strict_types=1);

namespace imissyouso\CsvAggregatorBundle\Storage;

interface StorageInterface extends \IteratorAggregate
{
    public function put(string $key, array $values): void;

    public function setValuesPerRow(int $valuesPerRow): void;

    public function all(): \Iterator;

    public function drop(): void;
}
