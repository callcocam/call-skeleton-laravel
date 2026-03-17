<?php

/**
 * Created by Claudio Campos.
 * User: callcocam, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Callcocam\LaravelRaptorPlanogram\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Request de validacao para geracao de planogramas com IA.
 */
class IAGeneratePlanogramRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'strategy' => ['required', 'string', 'in:abc,sales,margin,mix'],
            'use_existing_analysis' => ['nullable', 'boolean'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'min_facings' => ['nullable', 'integer', 'min:1', 'max:10'],
            'max_facings' => ['nullable', 'integer', 'min:1', 'max:20'],
            'group_by_subcategory' => ['nullable', 'boolean'],
            'include_products_without_sales' => ['nullable', 'boolean'],
            'table_type' => ['nullable', 'string', 'in:sales,monthly_summaries'],
            'category_id' => ['nullable', 'string', 'size:26'],
            'subcategory_id' => ['nullable', 'string', 'size:26'],
            'brand_id' => ['nullable', 'string', 'size:26'],
            'respect_seasonality' => ['nullable', 'boolean'],
            'apply_visual_grouping' => ['nullable', 'boolean'],
            'intelligent_ordering' => ['nullable', 'boolean'],
            'load_balancing' => ['nullable', 'boolean'],
            'additional_instructions' => ['nullable', 'string', 'max:1000'],
            'model' => ['nullable', 'string', 'in:gpt-4o,gpt-4o-mini,claude-sonnet-4-20250514,claude-3-5-haiku-latest,claude-sonnet-4-6'],
            'max_tokens' => ['nullable', 'integer', 'min:1000', 'max:16000'],
            'temperature' => ['nullable', 'numeric', 'min:0', 'max:2'],
        ];
    }

    public function messages(): array
    {
        return [
            'category_id.required' => 'Categoria e obrigatoria',
            'category_id.size' => 'ID de categoria invalido',
            'strategy.required' => 'Estrategia de geracao e obrigatoria',
            'strategy.enum' => 'Estrategia invalida. Use: sales, margin, mix ou abc',
            'subcategory_id.size' => 'ID de subcategoria invalido',
            'brand_id.size' => 'ID de marca invalido',
            'additional_instructions.max' => 'Instrucoes adicionais devem ter no maximo 1000 caracteres',
            'model.in' => 'Modelo de IA invalido',
            'max_tokens.min' => 'Tokens minimos: 1000',
            'max_tokens.max' => 'Tokens maximos: 16000',
            'temperature.min' => 'Temperatura minima: 0',
            'temperature.max' => 'Temperatura maxima: 2',
        ];
    }

    public function attributes(): array
    {
        return [
            'category_id' => 'categoria',
            'strategy' => 'estrategia',
            'subcategory_id' => 'subcategoria',
            'brand_id' => 'marca',
            'respect_seasonality' => 'respeitar sazonalidade',
            'apply_visual_grouping' => 'agrupamento visual',
            'intelligent_ordering' => 'ordenacao inteligente',
            'load_balancing' => 'balanceamento',
            'additional_instructions' => 'instrucoes adicionais',
            'model' => 'modelo de IA',
            'max_tokens' => 'tokens maximos',
            'temperature' => 'temperatura',
        ];
    }
}
