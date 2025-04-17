<?php

declare(strict_types=1);

namespace App\Service;

use Elastica\Query\BoolQuery;
use Elastica\Query\MultiMatch;
use Elastica\Query\Term;
use FOS\ElasticaBundle\Finder\PaginatedFinderInterface;
use Pagerfanta\PagerfantaInterface;

class SurveySearchService
{
    public function __construct(
        private readonly PaginatedFinderInterface $surveyFinder
    ) {}

    public function search(string $query, int $page, int $limit): PagerfantaInterface
    {
        $boolQuery = new BoolQuery();

        if ($query !== '') {
            $multiMatch = new MultiMatch();
            $multiMatch->setQuery($query);
            $multiMatch->setFields(['name', 'description']);
            $boolQuery->addMust($multiMatch);
        }

        $boolQuery->addFilter(new Term(['isActive' => true]));

        $paginator = $this->surveyFinder->findPaginated($boolQuery);

        $paginator->setMaxPerPage($limit);
        $paginator->setCurrentPage($page);

        return $paginator;
    }
}
