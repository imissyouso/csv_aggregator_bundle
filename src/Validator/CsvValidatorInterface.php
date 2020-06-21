<?php
declare(strict_types=1);

namespace imissyouso\CsvAggregatorBundle\Validator;

interface CsvValidatorInterface
{
    public function validateHeader(array $headerRow): void;
}
