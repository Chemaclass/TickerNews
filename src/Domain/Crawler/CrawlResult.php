<?php

declare(strict_types=1);

namespace Chemaclass\TickerNews\Domain\Crawler;

use Chemaclass\TickerNews\Domain\ReadModel\Company;

final class CrawlResult
{
    /** @var array<string,Company> */
    private array $companiesGroupedBySymbol;

    public function __construct(array $companiesGroupedBySymbol)
    {
        $this->companiesGroupedBySymbol = $companiesGroupedBySymbol;
    }

    public function getCompany(string $symbol): Company
    {
        return $this->companiesGroupedBySymbol[$symbol] ?? Company::empty();
    }

    public function getCompaniesGroupedBySymbol(): array
    {
        return $this->companiesGroupedBySymbol;
    }
}
