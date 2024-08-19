<?php

namespace App\Services;

use Goutte\Client;
use Symfony\Component\DomCrawler\Crawler;
use Illuminate\Support\Facades\Log;

class BrasileiraoScraperService
{
    public function scrapeClassificacao()
    {
        $url = 'https://www.uol.com.br/esporte/futebol/campeonatos/brasileirao/';
        $client = new Client();

        try {
            $crawler = $client->request('GET', $url);
            
            Log::info('Página acessada com sucesso');

            $times = $crawler->filter('table.data-table.name tbody tr')->each(function (Crawler $node) {
                $posicao = $node->filter('td.team .position')->text();
                $nome = $node->filter('td.team .name div.visible-sm')->text();
                $sigla = $node->filter('td.team .name div.visible-xs')->text();

                Log::info("Dados extraídos para o time", [
                    'posicao' => $posicao,
                    'nome' => $nome,
                    'sigla' => $sigla
                ]);

                return [
                    'posicao' => trim($posicao),
                    'nome' => trim($nome),
                    'sigla' => trim($sigla),
                ];
            });

            $times = array_filter($times);

            if (empty($times)) {
                Log::error('Nenhum time encontrado após o processamento');
                throw new \Exception('Não foi possível encontrar dados dos times na tabela de classificação.');
            }

            Log::info('Scraping concluído com sucesso', ['total_times' => count($times)]);

            return $times;
        } catch (\Exception $e) {
            Log::error('Erro durante o scraping', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
}