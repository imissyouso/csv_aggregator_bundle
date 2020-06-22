<?php
declare(strict_types=1);

namespace imissyouso\CsvAggregatorBundle\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class AggregateCsvCommandTest extends KernelTestCase
{
    public function testExecute()
    {
        $kernel = static::createKernel();
        $application = new Application($kernel);

        $command = $application->find('csv:aggregate');
        $commandTester = new CommandTester($command);

        $outPath = __DIR__.'/../data/out/result.csv';
        $commandTester->execute([
            'sourcePath' => __DIR__.'/../data/in',
            'outPath' => $outPath,
            'header' => 'date;A;B;C'
        ]);

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('CSV has been generated with success!', $output);

        $resultFileContent = file_get_contents($outPath);

        $expectedContent = "date;A;B;C\n2;0;0;0\n2018-03-01;24;32;23.05\n2018-03-02;15;21;18.18\n2018-03-03;4;8;4.24\n";

        $this->assertEquals($expectedContent, $resultFileContent);

        unlink($outPath);
    }

    // @TODO add negative tests
}
