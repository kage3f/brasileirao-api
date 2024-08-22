<?php

namespace App\Http\Controllers;

use App\Services\BrasileiraoRodadasService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BrasileiraoRodadasController extends Controller
{
    protected $rodadasService;

    public function __construct(BrasileiraoRodadasService $rodadasService)
    {
        $this->rodadasService = $rodadasService;
    }

    public function getRodada(Request $request): JsonResponse
    {
        try {
            $rodadaNumero = $request->input('rodada');
            $data = $this->rodadasService->getRodada($rodadaNumero);
            return response()->json(['success' => true, 'data' => $data]);
        } catch (\Exception $e) {
            Log::error('Erro ao obter rodada do Brasileirão', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
}