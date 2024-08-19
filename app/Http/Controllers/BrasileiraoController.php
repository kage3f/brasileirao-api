<?php

namespace App\Http\Controllers;

use App\Services\BrasileiraoScraperService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class BrasileiraoController extends Controller
{
    protected $scraperService;

    public function __construct(BrasileiraoScraperService $scraperService)
    {
        $this->scraperService = $scraperService;
    }

    public function getClassificacao(): JsonResponse
    {
        try {
            $classificacao = $this->scraperService->scrapeClassificacao();
            return response()->json(['success' => true, 'data' => $classificacao]);
        } catch (\Exception $e) {
            Log::error('Erro ao obter classificação do Brasileirão', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
}