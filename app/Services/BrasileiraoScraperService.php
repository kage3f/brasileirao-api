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

            $times = $crawler->filter('table.data-table.name tbody tr')->each(function (Crawler $node, $i) use ($crawler) {
                $posicao = $node->filter('.position')->text();
                $nome = $node->filter('.name div.visible-sm')->text();
                $sigla = $node->filter('.name div.visible-xs')->text();
                $brasao = $node->filter('.team-crest img')->attr('src');

                // Buscando as estatísticas correspondentes na tabela de pontos
                $statsNode = $crawler->filter('table.data-table.score tbody tr')->eq($i);
                $pontos = $statsNode->filter('td')->eq(0)->text();
                $jogos = $statsNode->filter('td')->eq(1)->text();
                $vitorias = $statsNode->filter('td')->eq(2)->text();
                $empates = $statsNode->filter('td')->eq(3)->text();
                $derrotas = $statsNode->filter('td')->eq(4)->text();
                $saldoGols = $statsNode->filter('td')->eq(7)->text();

                Log::info("Dados extraídos para o time", [
                    'posicao' => $posicao,
                    'nome' => $nome,
                    'sigla' => $sigla,
                    'brasao' => $brasao,
                    'pontos' => $pontos,
                    'jogos' => $jogos,
                    'vitorias' => $vitorias,
                    'empates' => $empates,
                    'derrotas' => $derrotas,
                    'saldoGols' => $saldoGols
                ]);

                return [
                    'posicao' => trim($posicao),
                    'nome' => trim($nome),
                    'sigla' => trim($sigla),
                    'brasao' => $brasao,
                    'pontos' => (int)$pontos,
                    'jogos' => (int)$jogos,
                    'vitorias' => (int)$vitorias,
                    'empates' => (int)$empates,
                    'derrotas' => (int)$derrotas,
                    'saldoGols' => (int)$saldoGols
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