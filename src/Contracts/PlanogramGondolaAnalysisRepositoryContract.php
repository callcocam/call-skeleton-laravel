<?php

namespace Callcocam\LaravelRaptorPlanogram\Contracts;

interface PlanogramGondolaAnalysisRepositoryContract
{
    public function getLatestAbcAnalysis(string $gondolaId): ?array;

    public function getLatestStockAnalysis(string $gondolaId): ?array;
}
