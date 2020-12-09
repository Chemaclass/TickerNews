<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Company\CompanyCrawler;
use App\Company\Crawler\CrawlerInterface;
use App\Company\ReadModel\Company;
use App\Company\ReadModel\TickerSymbol;
use App\FinYahoo;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

final class FinYahooTest extends TestCase
{
    private const EXAMPLE_REQUEST_URL = 'https://example.url.com/%s/';

    /** @test */
    public function crawlEmptyStock(): void
    {
        $finYahoo = new FinYahoo(
            $this->mockHttpClient(),
            new CompanyCrawler(self::EXAMPLE_REQUEST_URL, [])
        );

        self::assertEmpty($finYahoo->crawlStock());
    }

    /** @test */
    public function crawlStockForOneTickerSymbolAndMultipleCrawlers(): void
    {
        $finYahoo = new FinYahoo(
            $this->mockHttpClient(),
            $this->createCompanyCrawler('key1', 'value1'),
            $this->createCompanyCrawler('key2', 'value2')
        );

        $actual = $finYahoo->crawlStock(
            new TickerSymbol('EXAMPLE_TICKER')
        );

        self::assertEquals([
            'EXAMPLE_TICKER' => new Company([
                'key1' => 'value1',
                'key2' => 'value2',
            ]),
        ], $actual);
    }

    /** @test */
    public function crawlStockForMultipleTickerSymbols(): void
    {
        $finYahoo = new FinYahoo(
            $this->mockHttpClient(),
            $this->createCompanyCrawler('key1', 'value1'),
        );

        $actual = $finYahoo->crawlStock(
            new TickerSymbol('EXAMPLE_TICKER_1'),
            new TickerSymbol('EXAMPLE_TICKER_2'),
        );

        self::assertEquals([
            'EXAMPLE_TICKER_1' => new Company([
                'key1' => 'value1',
            ]),
            'EXAMPLE_TICKER_2' => new Company([
                'key1' => 'value1',
            ]),
        ], $actual);
    }

    private function createCompanyCrawler(string $crawlerKey, string $extractedValue): CompanyCrawler
    {
        return new CompanyCrawler(
            self::EXAMPLE_REQUEST_URL,
            [
                $crawlerKey => new class($extractedValue) implements CrawlerInterface {
                    private string $value;

                    public function __construct(string $value)
                    {
                        $this->value = $value;
                    }

                    public function crawlHtml(string $html): string
                    {
                        return $this->value;
                    }
                },
            ]
        );
    }

    private function mockHttpClient(string $responseBody = ''): HttpClientInterface
    {
        $response = $this->createMock(ResponseInterface::class);
        $response->method('getContent')->willReturn($responseBody);

        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient->method('request')->willReturn($response);

        return $httpClient;
    }
}
