<?php

declare(strict_types=1);

namespace Chemaclass\StockTicker\Infrastructure\Command;

use Chemaclass\StockTicker\StockTickerConfig;
use Chemaclass\StockTicker\StockTickerFacade;
use Chemaclass\StockTicker\StockTickerFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class CrawlCommand extends Command
{
    private const DEFAULT_MAX_NEWS_TO_FETCH = 2;

    protected function configure(): void
    {
        $this
            ->setName('crawl')
            ->setDescription('It crawls the websites and fetch their latest news')
            ->addArgument(
                'symbols',
                InputArgument::IS_ARRAY|InputArgument::REQUIRED,
                'Which stock symbols do you want to crawl?'
            )
            ->addOption(
                'maxNews',
                'm',
                InputArgument::OPTIONAL,
                'Which stock symbols do you want to crawl?',
                self::DEFAULT_MAX_NEWS_TO_FETCH
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $symbols = (array) $input->getArgument('symbols');

        $txt = sprintf('Crawling stock %s...', implode(', ', $symbols));
        $output->writeln($txt);
        $facade = $this->createStockTickerFacade();

        $crawlResult = $facade->crawlStock($symbols, (int) $input->getOption('maxNews'));

        ResultOutputPrinter::printCrawResult($output, $crawlResult);

        return Command::SUCCESS;
    }

    private function createStockTickerFacade(): StockTickerFacade
    {
        return new StockTickerFacade(
            new StockTickerFactory(new StockTickerConfig())
        );
    }
}
