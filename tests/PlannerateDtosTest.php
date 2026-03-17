<?php

use Callcocam\LaravelRaptorPlanogram\DTOs\AutoGenerate\AutoGenerateConfigDTO;
use Callcocam\LaravelRaptorPlanogram\DTOs\IAGenerate\IAGenerateConfigDTO;
use Callcocam\LaravelRaptorPlanogram\DTOs\IAGenerate\PlanogramContextDTO;
use Callcocam\LaravelRaptorPlanogram\DTOs\SectionGenerate\SectionAllocationItemDTO;
use Callcocam\LaravelRaptorPlanogram\DTOs\SectionGenerate\SectionAllocationResultDTO;

it('creates auto generate config dto from array', function () {
    $dto = AutoGenerateConfigDTO::fromArray([
        'strategy' => 'mix',
        'min_facings' => 2,
        'max_facings' => 8,
        'category_id' => 'cat-1',
    ]);

    expect($dto->strategy)->toBe('mix')
        ->and($dto->minFacings)->toBe(2)
        ->and($dto->maxFacings)->toBe(8)
        ->and($dto->categoryId)->toBe('cat-1');
});

it('serializes ia generate config dto to expected keys', function () {
    $dto = IAGenerateConfigDTO::fromArray([
        'strategy' => 'abc',
        'category_id' => 'cat-1',
        'temperature' => 0.5,
    ]);

    expect($dto->toArray())->toHaveKeys([
        'category_id',
        'strategy',
        'subcategory_id',
        'brand_id',
        'respect_seasonality',
        'apply_visual_grouping',
        'intelligent_ordering',
        'load_balancing',
        'additional_instructions',
        'model',
        'max_tokens',
        'temperature',
    ]);
});

it('normalizes section allocation result from agent response', function () {
    $result = SectionAllocationResultDTO::fromAgentResponse([
        'reasoning' => 'allocation reasoning',
        'allocation' => [
            ['shelf_id' => 'shelf-1', 'product_id' => 'product-1', 'facings' => 3],
        ],
        'unallocated' => ['product-2'],
    ]);

    expect($result->allocation)->toHaveCount(1)
        ->and($result->allocation[0])->toBeInstanceOf(SectionAllocationItemDTO::class)
        ->and($result->unallocatedByReason['unknown'])->toBe(1)
        ->and($result->unallocatedReasonByProduct)->toHaveKey('product-2');
});

it('computes context stats summary from provided data', function () {
    $context = new PlanogramContextDTO(
        gondolaId: 'gondola-1',
        gondolaData: ['id' => 'gondola-1'],
        shelves: [
            ['available_space' => 1000],
            ['available_space' => 500],
        ],
        products: [
            ['dimensions' => ['width' => 10, 'depth' => 5], 'suggested_facings' => 2, 'abc_class' => 'A', 'score' => 10],
            ['dimensions' => ['width' => 8, 'depth' => 5], 'suggested_facings' => 1, 'abc_class' => 'B', 'score' => 5],
        ],
        categoryHierarchy: [],
        merchandisingRules: [],
    );

    $summary = $context->getStatsSummary();

    expect($summary)->toHaveKeys([
        'total_shelves',
        'total_shelf_area_cm2',
        'total_products',
        'total_product_area_cm2',
        'space_utilization_estimate',
        'abc_distribution',
        'avg_product_score',
    ])->and($summary['total_shelves'])->toBe(2)
        ->and($summary['total_products'])->toBe(2)
        ->and($summary['abc_distribution'])->toHaveKeys(['A', 'B']);
});