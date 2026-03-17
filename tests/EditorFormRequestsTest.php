<?php

use Callcocam\LaravelRaptorPlanogram\Http\Requests\Editor\SaveChangesRequest;
use Callcocam\LaravelRaptorPlanogram\Http\Requests\Editor\StoreGondolaRequest;
use Callcocam\LaravelRaptorPlanogram\Http\Requests\Editor\UpdateGondolaRequest;

it('authorizes all migrated editor requests', function () {
    expect((new SaveChangesRequest)->authorize())->toBeTrue()
        ->and((new StoreGondolaRequest)->authorize())->toBeTrue()
        ->and((new UpdateGondolaRequest)->authorize())->toBeTrue();
});

it('keeps expected save changes request rules', function () {
    $rules = (new SaveChangesRequest)->rules();

    expect($rules)->toHaveKeys([
        'gondola_id',
        'changes',
        'changes.*.type',
        'changes.*.entityType',
        'changes.*.entityId',
        'changes.*.data',
        'changes.*.timestamp',
        'metadata',
        'metadata.total_changes',
        'metadata.last_modified',
    ]);
});

it('keeps expected gondola request rules and messages', function () {
    $storeRules = (new StoreGondolaRequest)->rules();
    $updateRules = (new UpdateGondolaRequest)->rules();
    $storeMessages = (new StoreGondolaRequest)->messages();
    $updateMessages = (new UpdateGondolaRequest)->messages();

    expect($storeRules)->toHaveKeys(['gondolaName', 'flow', 'status', 'numShelves', 'assignedUserId'])
        ->and($updateRules)->toHaveKeys(['gondolaName', 'location', 'side', 'scaleFactor', 'flow', 'status'])
        ->and($storeMessages)->toHaveKey('gondolaName.required')
        ->and($updateMessages)->toHaveKey('status.in');
});
