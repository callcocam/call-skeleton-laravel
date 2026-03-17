<?php

namespace Callcocam\LaravelRaptorPlanogram\Http\Requests\Editor;

use Illuminate\Foundation\Http\FormRequest;

class SaveChangesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'gondola_id' => 'required|string|exists:gondolas,id',
            'changes' => 'required|array|min:1',
            'changes.*.type' => [
                'required',
                'string',
                'in:shelf_create,shelf_move,shelf_transfer,shelf_update,section_update,product_placement,layer_create,layer_update,segment_update,product_update,product_removal,gondola_update,gondola_scale,gondola_alignment,gondola_flow,segment_copy,segment_update,segment_transfer,segment_reorder',
            ],
            'changes.*.entityType' => 'required|string|in:shelf,section,product,layer,segment,gondola',
            'changes.*.entityId' => 'required|string',
            'changes.*.data' => 'required|array',
            'changes.*.timestamp' => 'required|integer',
            'metadata' => 'required|array',
            'metadata.total_changes' => 'required|integer',
            'metadata.last_modified' => 'required|integer',
        ];
    }
}