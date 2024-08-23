<?php

namespace App\Services;

use Goutte\Client;
use Symfony\Component\DomCrawler\Crawler;
use Illuminate\Support\Facades\Log;

class BrasileiraoRodadasService
{
    protected $client;
    protected $url = 'https://www.gazetaesportiva.com/campeonatos/brasileiro-serie-a/';

    public function __construct()
    {
        $this->client = new Client();
    }

    public function getRodada($numero = null)
    {
        try {
            $crawler = $this->client->request('GET', $this->url);
            Log::info('URL acessada', ['url' => $this->url]);
            
            $rodadaAtual = $this->getCurrentRodada($crawler);
            
            if ($numero === null) {
                $numero = $rodadaAtual;
                Log::info('Usando rodada mais recente encontrada', ['rodada' => $numero]);
            } else {
                $numero = (int) $numero;
                Log::info('Usando rodada fornecida pelo usuário', ['rodada' => $numero]);
            }
    
            $rodadaDiv = $crawler->filter(".rodadas_grupo_A_numero_rodada_{$numero}")->first();
    
            if ($rodadaDiv->count() === 0) {
                Log::warning('Div da rodada não encontrada', ['rodada' => $numero]);
                throw new \Exception("Rodada {$numero} não encontrada.");
            }
    

            $jogos = $rodadaDiv->filter('.table__games__item')->each(function (Crawler $node) {
                $date = $node->filter('.date')->text();
                
                $homeTeamInfo = $this->getTeamInfo($node, '.home');
                $awayTeamInfo = $this->getTeamInfo($node, '.guest');
                
                $score = $node->filter('.score');
                $homeScore = $this->getScore($score, '.score__home');
                $awayScore = $this->getScore($score, '.score__guest');

                $jogo = [
                    'date' => trim($date),
                    'home_team' => $homeTeamInfo['name'],
                    'away_team' => $awayTeamInfo['name'],
                    'home_score' => $homeScore,
                    'away_score' => $awayScore,
                ];

                Log::info('Dados do jogo capturados:', $jogo);

                return $jogo;
            });

            return [
                'rodada' => $numero,
                'jogos' => $jogos,
            ];
        } catch (\Exception $e) {
            Log::error('Erro ao buscar rodada do Brasileirão', [
                'rodada' => $numero,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    private function getCurrentRodada(Crawler $crawler)
    {
        Log::info('Iniciando getCurrentRodada');
        
        $rodadaElement = $crawler->filter('[class*="rodadas_grupo_A_numero_rodada_"].mostrarRodada')->first();
        
        if ($rodadaElement->count() === 0) {
            Log::warning('Elemento da rodada atual não encontrado');
            throw new \Exception("Elemento da rodada atual não encontrado.");
        }
    
        $classNames = $rodadaElement->attr('class');
        preg_match('/rodadas_grupo_A_numero_rodada_(\d+)/', $classNames, $matches);
        
        if (!isset($matches[1])) {
            Log::error('Não foi possível extrair o número da rodada', ['classes' => $classNames]);
            throw new \Exception("Não foi possível determinar a rodada atual.");
        }
    
        $rodadaAtual = (int) $matches[1];
        Log::info('Rodada atual extraída com sucesso', ['rodada' => $rodadaAtual]);
        return $rodadaAtual;
    }

    private function getTeamInfo(Crawler $node, $selector)
    {
        $teamNode = $node->filter($selector);
        $teamName = $teamNode->filter('.team-link')->last()->text();
        
        if (empty(trim($teamName))) {
            $teamName = $teamNode->filter('.team-link')->first()->text();
        }

        $crestNode = $teamNode->filter('img');
        $crestUrl = $crestNode->count() > 0 ? $crestNode->attr('src') : null;

        return [
            'name' => trim($teamName),
            'crest' => $crestUrl
        ];
    }

    private function getScore(Crawler $node, $selector)
    {
        if ($node->filter($selector)->count() === 0) {
            return null;
        }
        $score = $node->filter($selector)->text();
        return $score === '' ? null : $score;
    }
}