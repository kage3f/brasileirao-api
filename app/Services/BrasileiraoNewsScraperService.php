<?php

namespace App\Services;

use Goutte\Client;
use Symfony\Component\DomCrawler\Crawler;
use Illuminate\Support\Facades\Log;
use Illuminate\Pagination\LengthAwarePaginator;

class BrasileiraoNewsScraperService
{
    protected $client;
    protected $baseUrl = 'https://www.lance.com.br/brasileirao/mais-noticias';
    protected $allNews = [];

    public function __construct()
    {
        $this->client = new Client();
    }

    public function scrapeNews($page = 1, $perPage = 20)
    {
        try {
            $this->ensureEnoughNews($page, $perPage);

            $offset = ($page - 1) * $perPage;
            $itemsForCurrentPage = array_slice($this->allNews, $offset, $perPage);
            
            return new LengthAwarePaginator(
                $itemsForCurrentPage,
                count($this->allNews),
                $perPage,
                $page,
                ['path' => request()->url(), 'query' => request()->query()]
            );

        } catch (\Exception $e) {
            Log::error('Erro durante o scraping de notícias', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    private function ensureEnoughNews($page, $perPage)
    {
        $requiredNews = $page * $perPage;
        $currentPage = intdiv(count($this->allNews), 20) + 1; // Assumindo que cada página do site tem 20 notícias

        while (count($this->allNews) < $requiredNews) {
            $url = $currentPage === 1 ? $this->baseUrl : "{$this->baseUrl}/{$currentPage}";
            $crawler = $this->client->request('GET', $url);
            Log::info('URL acessada', ['url' => $url]);

            $pageNews = $this->extractNewsFromPage($crawler);
            $this->allNews = array_merge($this->allNews, $pageNews);
            
            if (empty($pageNews)) {
                break; // Se não há mais notícias, interrompe o loop
            }

            $currentPage++;
        }
    }

    private function extractNewsFromPage(Crawler $crawler)
    {
        return $crawler->filter('ul.tab-m\\:flex li')->each(function (Crawler $node) {
            $linkNode = $node->filter('a')->first();
            if (!$linkNode->count()) {
                return null;
            }

            $link = $linkNode->attr('href');
            $fullLink = "https://www.lance.com.br" . $link;
            
            $imageUrl = $node->filter('img')->count() ? $node->filter('img')->attr('src') : null;
            $category = $node->filter('span.flex.flex-col')->count() ? $node->filter('span.flex.flex-col')->text() : '';
            $title = $node->filter('h3')->count() ? $node->filter('h3')->text() : '';
            $date = $node->filter('div.font-normal.text-\\[0\\.875rem\\]')->count() ? $node->filter('div.font-normal.text-\\[0\\.875rem\\]')->text() : '';

            return [
                'title' => trim($title),
                'category' => trim($category),
                'date' => trim($date),
                'link' => $fullLink,
                'imageUrl' => $imageUrl,
            ];
        });
    }
}