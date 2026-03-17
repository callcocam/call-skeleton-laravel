<?php

/**
 * Created by Claudio Campos.
 * User: callcocam, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Callcocam\LaravelRaptorPlanogram\Http\Controllers\Editor;

use Callcocam\LaravelRaptorPlanogram\Models\Segment;
use Callcocam\LaravelRaptor\Http\Controllers\ResourceController;
use Illuminate\Http\Request;

class SegmentController extends ResourceController
{
    protected function getResourceLabel(): ?string
    {
        return 'Segmento';
    }

    protected function resourcePath(): ?string
    {
        return 'tenant';
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'width' => 'nullable|numeric|min:0',
            'height' => 'nullable|numeric|min:0',
            'quantity' => 'nullable|integer|min:1',
            'ordering' => 'nullable|integer|min:0',
        ]);

        $segment = Segment::findOrFail($id);
        $segment->update($validated);

        return redirect()->back()->with([
            'success' => 'Segmento atualizado com sucesso.',
            'segment' => $segment,
        ]);
    }
}
