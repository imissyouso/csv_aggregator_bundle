<?php
declare(strict_types=1);

namespace imissyouso\CsvAggregatorBundle\Tests\Unit;

use imissyouso\CsvAggregatorBundle\AggregateStrategy\AggregateStrategyInterface;
use imissyouso\CsvAggregatorBundle\HashKeyConverter\SimpleHashStringConverter;
use imissyouso\CsvAggregatorBundle\Storage\HashMapFileStorage\HashMapFileStorage;
use Iterator;
use PHPUnit\Framework\TestCase;

class HashMapFileStorageTest extends TestCase
{
    public function testHashMapStorage(): void
    {
        $valuesPerRow = 3;
        $tmpFile = __DIR__.'/../data/out/tmp.bin';
        $bucketSizes = [1000, 2, 1, 100, 10000];
        foreach ($bucketSizes as $size) {
            $storage = $this->makeStorage($tmpFile, null, $size, $valuesPerRow);

            $map = [];
            foreach ($this->sampleData() as $key => $sampleValues) {
                if(!isset($map[$key])){
                    $map[$key] = array_fill(0, $valuesPerRow, 0);
                }

                foreach ($map[$key] as $i => $aggregatedValue){
                    $map[$key][$i] = $sampleValues[$i];
                }

                $storage->put($key, $sampleValues);

                $storageData = iterator_to_array($storage);

                $this->assertEquals($map, $storageData);
            }

            $storage->drop();

            $this->assertFileNotExists($tmpFile);
        }
    }

    public function testHashMapStorageWithAggregator(): void
    {
        $valuesPerRow = 3;
        $tmpFile = __DIR__.'/../data/out/tmp.bin';
        $storage = $this->makeStorage(
            $tmpFile,
            new class implements AggregateStrategyInterface {
                public function apply($a, $b)
                {
                    return $a + $b;
                }
            },
            $valuesPerRow
        );

        $map = [];
        foreach ($this->sampleData() as $key => $sampleValues) {
            if(!isset($map[$key])){
                $map[$key] = array_fill(0, $valuesPerRow, 0);
            }

            foreach ($map[$key] as $i => $aggregatedValue){
                $map[$key][$i] += $sampleValues[$i];
            }

            $storage->put($key, $sampleValues);

            $storageData = iterator_to_array($storage);

            $this->assertEquals($map, $storageData);
        }
        $storage->drop();
        $this->assertFileNotExists($tmpFile);
    }

    private function sampleData(): Iterator
    {
        yield '2020-01-01' => [1, 2, -3.01];
        yield '2020-01-01' => [2, 2, 5, 29];
        yield '2020-02-01' => [-0.9, 1];
        yield '2020-01-01' => [2, 8, -99];
        yield '2020-03-01' => [0.8, 2, 6];
        yield '2020-02-01' => [1];
        yield '2020-04-01' => [15, 0.8];
        yield '2020-10-01' => [2, -0.33, 88];
    }

    private function makeStorage(
        string $tmpFileDir,
        AggregateStrategyInterface $aggregateStrategy = null,
        $bucketSize = 1024,
        $valuesPerRow = 3
    ): HashMapFileStorage {
        return new HashMapFileStorage(
            new SimpleHashStringConverter(),
            $tmpFileDir,
            $aggregateStrategy,
            $bucketSize,
            $valuesPerRow
        );
    }
}
