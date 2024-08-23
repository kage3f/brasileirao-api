<?php

namespace App\Http\Controllers;

use App\Services\BrasileiraoNewsScraperService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BrasileiraoNewsController extends Controller
{
    protected $scraperService;

    public function __construct(BrasileiraoNewsScraperService $scraperService)
    {
        $this->scraperService = $scraperService;
    }

    public function getNews(Request $request): JsonResponse
    {
        try {
            $page = $request->input('page', 1);
            $perPage = $request->input('per_page', 20);

            $news = $this->scraperService->scrapeNews($page, $perPage);

            return response()->json([
                'success' => true,
                'data' => $news->items(),
                'meta' => [
                    'current_page' => $news->currentPage(),
                    'from' => $news->firstItem(),
                    'last_page' => $news->lastPage(),
                    'per_page' => $news->perPage(),
                    'to' => $news->lastItem(),
                    'total' => $news->total(),
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao obter notícias do Brasileirão', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
}