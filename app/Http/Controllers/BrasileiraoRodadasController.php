<?php

namespace App\Http\Controllers;

use App\Services\BrasileiraoRodadasService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BrasileiraoRodadasController extends Controller
{
    protected $rodadasService;

    public function __construct(BrasileiraoRodadasService $rodadasService)
    {
        $this->rodadasService = $rodadasService;
    }

    public function getRodada(Request $request): JsonResponse
    {
        $rodada = $request->input('rodada', 1);

        try {
            $data = $this->rodadasService->getRodada($rodada);
            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 404);
        }
    }
}