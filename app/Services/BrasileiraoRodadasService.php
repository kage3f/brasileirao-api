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

    public function getRodada($numero)
    {
        try {
            $crawler = $this->client->request('GET', $this->url);
            
            $rodadaDiv = $crawler->filter(".rodadas_grupo_A_numero_rodada_{$numero}")->first();

            if ($rodadaDiv->count() === 0) {
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
                    'home_team_crest' => $homeTeamInfo['crest'],
                    'away_team' => $awayTeamInfo['name'],
                    'away_team_crest' => $awayTeamInfo['crest'],
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