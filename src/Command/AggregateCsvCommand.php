<?php
declare(strict_types=1);

namespace imissyouso\CsvAggregatorBundle\Command;

use imissyouso\CsvAggregatorBundle\Factory\RecursiveCsvMetricsAggregatorFactory;
use imissyouso\CsvAggregatorBundle\RowProcessor\MetricsRowProcessorInterface;
use imissyouso\CsvAggregatorBundle\Storage\StorageInterface;
use imissyouso\CsvAggregatorBundle\Writer\SimpleCsvWriter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class AggregateCsvCommand extends Command
{
    public const COMMAND_NAME = 'csv:aggregate';

    /**
     * @var SymfonyStyle
     */
    private $io;

    /**
     * @var StorageInterface
     */
    private $storage;

    /**
     * @var string
     */
    private $csvDelimiter;

    /**
     * @var MetricsRowProcessorInterface
     */
    private $rowProcessor;

    /**
     * AggregateCsvCommand constructor.
     * @param StorageInterface $storage
     * @param MetricsRowProcessorInterface $rowProcessor
     * @param string $csvDelimiter
     */
    public function __construct(StorageInterface $storage, MetricsRowProcessorInterface $rowProcessor, string $csvDelimiter)
    {
        parent::__construct(self::COMMAND_NAME);
        $this->storage = $storage;
        $this->csvDelimiter = $csvDelimiter;
        $this->rowProcessor = $rowProcessor;
    }

    protected function configure(): void
    {
        parent::configure();
        $this
            ->setDescription('Recursive csv metrics aggregator')
            ->addArgument('sourcePath', InputArgument::REQUIRED, 'CSV directory path')
            ->addArgument('outPath', InputArgument::REQUIRED, 'Result CSV file path')
            ->addArgument('header', InputArgument::OPTIONAL, 'CSV header pattern', 'date|A|B|C');
    }

    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        parent::initialize($input, $output);
        $this->io = new SymfonyStyle($input, $output);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $expectedHeader = explode('|', $input->getArgument('header'));
        $sourceDir = $input->getArgument('sourcePath');
        $outPath = $input->getArgument('outPath');

        (new RecursiveCsvMetricsAggregatorFactory(
            $this->storage,
            new SimpleCsvWriter($outPath, $this->csvDelimiter),
            $sourceDir,
            $expectedHeader,
            $this->csvDelimiter,
            $this->rowProcessor
        ))->make()->run();

        $this->io->success(sprintf('CSV has been generated with success! Check the %s file', $outPath));

        return 0;
    }
}
