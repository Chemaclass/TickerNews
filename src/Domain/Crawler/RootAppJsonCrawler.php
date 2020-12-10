<?php

declare(strict_types=1);

namespace Chemaclass\FinanceYahoo\Domain\Crawler;

use Chemaclass\FinanceYahoo\Domain\Crawler\JsonExtractor\JsonExtractorInterface;
use Chemaclass\FinanceYahoo\Domain\ReadModel\Site;
use Chemaclass\FinanceYahoo\Domain\ReadModel\Ticker;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * @see "data/RootAppMainJsonExample.json" to see the structure of the `root.App.main` json.
 * @see https://jsoneditoronline.org/ to visualize and find what you are interested in.
 */
final class RootAppJsonCrawler implements SiteCrawlerInterface
{
    private const REQUEST_METHOD = 'GET';

    private const REQUEST_URL = 'https://finance.yahoo.com/quote/%s';

    /** @var JsonExtractorInterface[] */
    private array $jsonExtractors;

    public function __construct(JsonExtractorInterface ...$jsonExtractors)
    {
        $this->jsonExtractors = $jsonExtractors;
    }

    public function crawl(HttpClientInterface $httpClient, Ticker $ticker): Site
    {
        $url = sprintf(self::REQUEST_URL, $ticker->symbol());

        $html = $httpClient
            ->request(self::REQUEST_METHOD, $url)
            ->getContent();

        preg_match('/root\.App\.main\ =\ (?<json>.*);/m', $html, $matches);

        $json = (array) json_decode($matches['json'], true);
        $data = [];

        foreach ($this->jsonExtractors as $extractor) {
            $data[$extractor->name()] = $extractor->extractFromJson($json);
        }

        return new Site($data);
    }
}