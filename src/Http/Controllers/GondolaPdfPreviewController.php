<?php

namespace Callcocam\LaravelRaptorPlanogram\Http\Controllers;

use Callcocam\LaravelRaptorPlanogram\Contracts\PlanogramGondolaAnalysisRepositoryContract;
use Callcocam\LaravelRaptorPlanogram\Services\Printing\GondolaPrintService;
use Illuminate\Routing\Controller;
use Inertia\Inertia;
use Inertia\Response;

class GondolaPdfPreviewController extends Controller
{
    public function __construct(
        protected GondolaPrintService $printService,
        protected PlanogramGondolaAnalysisRepositoryContract $gondolaAnalysisRepository
    ) {}

    /**
     * Exibe preview da gôndola usando componentes Vue
     */
    public function show(string $gondolaId): Response
    {
        $data = $this->printService->prepareGondolaData($gondolaId);

        // Carregar análises mais recentes
        $abcAnalysis = $this->gondolaAnalysisRepository->getLatestAbcAnalysis($gondolaId);
        $stockAnalysis = $this->gondolaAnalysisRepository->getLatestStockAnalysis($gondolaId);

        return Inertia::render('tenant/plannerates/pdfPrintview', [
            'gondola' => $data['gondola'],
            'sections' => $data['sections'],
            'analysis' => [
                'abc' => $abcAnalysis,
                'stock' => $stockAnalysis,
            ],
        ]);
    }
}
