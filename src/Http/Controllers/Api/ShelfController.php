<?php

/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Callcocam\Plannerate\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Callcocam\Plannerate\Models\Shelf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ShelfController extends Controller
{


    public function segment(Request $request, Shelf $shelf,)
    {
        $validated = $request->all();

        // Processa atualização normal com possível adição de segmento/camada
        $segment = data_get($validated, 'segment');
        $layer = data_get($segment, 'layer');
        try {
            if ($segment) {
                DB::beginTransaction();
                $newSegment =  $shelf->segments()->create($segment);
                if ($newSegment) {
                    $newSegment->layer()->create($layer);
                }
                DB::commit();
            }
            return response()->json([
                'message' => 'Segmento criado com sucesso',
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Erro ao criar segmento: ' . $e->getMessage(),
            ], 500);
        }
    }
}
