<?php

namespace Callcocam\LaravelRaptorPlanogram\Http\Requests\Editor;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateGondolaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'gondolaName' => ['required', 'string', 'max:255'],
            'location' => ['required', 'string', 'max:255'],
            'side' => ['required', 'string', 'in:left,right,center'],
            'scaleFactor' => ['required', 'numeric', 'min:1'],
            'flow' => ['required', 'string', 'in:left_to_right,right_to_left'],
            'status' => ['required', 'string', 'in:draft,active,inactive'],
        ];
    }

    public function messages(): array
    {
        return [
            'gondolaName.required' => 'O nome da gôndola é obrigatório.',
            'location.required' => 'A localização é obrigatória.',
            'side.required' => 'O lado é obrigatório.',
            'side.in' => 'O lado deve ser esquerdo, direito ou centro.',
            'scaleFactor.required' => 'O fator de escala é obrigatório.',
            'scaleFactor.min' => 'O fator de escala deve ser no mínimo 1.',
            'flow.required' => 'O fluxo é obrigatório.',
            'flow.in' => 'O fluxo deve ser esquerda para direita ou direita para esquerda.',
            'status.required' => 'O status é obrigatório.',
            'status.in' => 'O status deve ser rascunho, ativo ou inativo.',
        ];
    }
}
