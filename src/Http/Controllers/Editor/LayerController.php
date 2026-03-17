<?php

/**
 * Created by Claudio Campos.
 * User: callcocam, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Callcocam\LaravelRaptorPlanogram\Http\Controllers\Editor;

use Callcocam\LaravelRaptorPlanogram\Models\Layer;
use Callcocam\LaravelRaptor\Http\Controllers\ResourceController;
use Illuminate\Http\Request;

class LayerController extends ResourceController
{
    protected function getResourceLabel(): ?string
    {
        return 'Camada';
    }

    protected function resourcePath(): ?string
    {
        return 'tenant';
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'quantity' => 'nullable|integer|min:1',
            'height' => 'nullable|numeric|min:0',
            'spacing' => 'nullable|numeric|min:0',
            'alignment' => 'nullable|string|in:left,center,right',
        ]);

        $layer = Layer::findOrFail($id);
        $layer->update($validated);

        return redirect()->back()->with([
            'success' => 'Prateira atualizada com sucesso.',
            'layer' => $layer->load('product'),
        ]);
    }

    public function destroy($id)
    {
        $layer = Layer::findOrFail($id);
        $segmentId = $layer->segment_id;

        $layer->delete();

        return redirect()->back()->with([
            'success' => 'Produto removido com sucesso',
            'deleted_layer_id' => $id,
            'segment_id' => $segmentId,
        ]);
    }
}
