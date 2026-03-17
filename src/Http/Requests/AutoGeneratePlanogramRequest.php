<?php

/**
 * Created by Claudio Campos.
 * User: callcocam, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Callcocam\LaravelRaptorPlanogram\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Request de validacao para geracao automatica de planogramas.
 */
class AutoGeneratePlanogramRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'strategy' => ['required', 'string', 'in:abc,sales,margin,mix'],
            'use_existing_analysis' => ['required', 'boolean'],
            'start_date' => ['nullable', 'date', 'required_if:use_existing_analysis,false'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date', 'required_if:use_existing_analysis,false'],
            'min_facings' => ['required', 'integer', 'min:1', 'max:10'],
            'max_facings' => ['required', 'integer', 'min:1', 'max:20', 'gte:min_facings'],
            'group_by_subcategory' => ['required', 'boolean'],
            'include_products_without_sales' => ['required', 'boolean'],
            'table_type' => ['required', 'string', 'in:sales,monthly_summaries'],
            'use_ai' => ['nullable', 'boolean'],
            'category_id' => ['nullable', 'string', 'exists:categories,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'strategy.required' => 'A estrategia de otimizacao e obrigatoria.',
            'strategy.in' => 'Estrategia invalida. Escolha: abc, sales, margin ou mix.',
            'start_date.required_if' => 'Data inicial e obrigatoria quando nao usar analise existente.',
            'start_date.date' => 'Data inicial invalida.',
            'end_date.required_if' => 'Data final e obrigatoria quando nao usar analise existente.',
            'end_date.date' => 'Data final invalida.',
            'end_date.after_or_equal' => 'Data final deve ser igual ou posterior a data inicial.',
            'min_facings.required' => 'Facings minimo e obrigatorio.',
            'min_facings.min' => 'Facings minimo deve ser pelo menos 1.',
            'min_facings.max' => 'Facings minimo nao pode ser maior que 10.',
            'max_facings.required' => 'Facings maximo e obrigatorio.',
            'max_facings.min' => 'Facings maximo deve ser pelo menos 1.',
            'max_facings.max' => 'Facings maximo nao pode ser maior que 20.',
            'max_facings.gte' => 'Facings maximo deve ser maior ou igual ao minimo.',
            'table_type.in' => 'Tipo de dados invalido. Escolha: sales ou monthly_summaries.',
            'category_id.exists' => 'Categoria invalida. A categoria selecionada nao existe.',
            'category_id.string' => 'Categoria deve ser uma string (ID).',
        ];
    }

    public function attributes(): array
    {
        return [
            'strategy' => 'estrategia',
            'use_existing_analysis' => 'usar analise existente',
            'start_date' => 'data inicial',
            'end_date' => 'data final',
            'min_facings' => 'facings minimo',
            'max_facings' => 'facings maximo',
            'group_by_subcategory' => 'agrupar por subcategoria',
            'include_products_without_sales' => 'incluir produtos sem vendas',
            'table_type' => 'tipo de dados',
            'use_ai' => 'usar IA por section',
            'category_id' => 'categoria',
        ];
    }
}
