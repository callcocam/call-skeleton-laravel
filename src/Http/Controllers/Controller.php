<?php

/**
 * Created by :author_name.
 * User: :author_username, author@domain.com
 * https://www.sigasmart.com.br
 */

namespace VendorName\Skeleton\Http\Controllers;

use VendorName\Skeleton\Facades\Skeleton; 
use VendorName\Skeleton\Support\Concerns\EvaluatesClosures; 
use Illuminate\Routing\Controller as BaseController;

/**
 * Class Controller
 * @package Callcocam\PapaLeguasReact\Http\Controllers
 */

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class Controller extends BaseController
{

    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    use EvaluatesClosures;


    /**
     * Get the ID of the controller.
     *
     * @return string
     */
    protected function getId()
    {
        return Skeleton::getId();
    }
    /**
     * Get the prefix for the controller routes.
     *
     * @return string
     */
    protected function getPrefix()
    {
        return Skeleton::getPrefix();
    }

    /**
     * Get view path index.
     */
    protected function getViewIndex()
    {
        return 'crud/index';
    }

    /**
     * Get view path create.
     */
    protected function getViewCreate()
    {
        return 'crud/create';
    }
    /**
     * Get view path edit.
     */
    protected function getViewEdit()
    {
        return 'crud/edit';
    }
    /**
     * Get view path show.
     */
    protected function getViewShow()
    {
        return 'crud/show';
    }

    /**
     * Get data for views.
     */
    protected function getDataForViews(Request $request)
    {
        return [
            'user' => auth()->user(),
            'permissions' => [],
            'request' => $request->query(),
        ];
    }

    protected function getDataForViewsCreate(Request $request)
    {
        return $this->getDataForViews($request);
    }

    protected function getDataForViewsEdit(Request $request, string $id)
    {
        return $this->getDataForViews($request);
    }

    protected function getDataForViewsShow(Request $request, string $id)
    {
        return $this->getDataForViews($request);
    }

    /**
     * Get route name for current controller
     */
    protected function getControllerName(): string
    {
        $controllerName = Str::snake(str_replace('Controller', '', class_basename(static::class)));
        $controllerName = Str::plural($controllerName);
        return $controllerName;
    }

    /**
     * Get the route name for the controller.
     *
     * @param string $suffix
     * @param string|null $prefix
     * @param string|null $controller
     * @return string
     */
    protected function getRouteName(string $suffix = 'index', ?string $prefix = null, ?string $controller = null): string
    {
        $prefix = $prefix ?? $this->getPrefix();
        $controller = $controller ?? $this->getControllerName();
        return "{$prefix}.{$controller}.{$suffix}";
    }

    protected function getTableKeyName(): string
    {
        $modelClass = $this->resolveModelClass(); 
        $modelName = class_basename($modelClass);
        return str($modelName)->append('Table')->toString();
    }

    // ==================================================================================
    // 🎯 MÉTODOS NECESSÁRIOS PARA COMPATIBILIDADE COM ADMINCONTROLLER
    // ==================================================================================

    /**
     * Resolver a classe do modelo baseado no nome do controller.
     */
    protected function resolveModelClass(): string
    {
        // Obter nome do controller sem o sufixo "Controller"
        $controllerName = str_replace('Controller', '', class_basename(static::class));
        
        // Converter para singular (Users -> User)
        $modelName = Str::singular($controllerName);
        
        // Tentar diferentes namespaces
        $possibleClasses = [
            "App\\Models\\{$modelName}",
            "App\\{$modelName}",
            "VendorName\\Skeleton\\Models\\{$modelName}",
        ];
        
        foreach ($possibleClasses as $class) {
            if (class_exists($class)) {
                return $class;
            }
        }
        
        throw new \Exception("Modelo não encontrado para o controller " . static::class . ". Tentou: " . implode(', ', $possibleClasses));
    }

    /**
     * Obter instância do modelo.
     */
    protected function getModelInstance()
    {
        $modelClass = $this->resolveModelClass();
        return new $modelClass;
    }

    /**
     * Obter a classe do modelo.
     */
    protected function getModelClass(): string
    {
        return $this->resolveModelClass();
    }

    /**
     * Criar um novo registro.
     */
    protected function createRecord(array $data)
    {
        $model = $this->getModelInstance();
        return $model->create($data);
    }

    /**
     * Atualizar um registro.
     */
    protected function updateRecord(string $id, array $data)
    {
        $model = $this->getModelInstance();
        $record = $model->findOrFail($id);
        $record->update($data);
        return $record;
    }

    /**
     * Deletar um registro.
     */
    protected function deleteRecord(string $id)
    {
        $model = $this->getModelInstance();
        $record = $model->findOrFail($id);
        return $record->delete();
    }

    /**
     * Encontrar um registro por ID.
     */
    protected function findRecord(string $id)
    {
        $model = $this->getModelInstance();
        return $model->findOrFail($id);
    }

    /**
     * Obter informações sobre relacionamentos detectados (placeholder).
     */
    protected function getDetectedRelationsInfo(): array
    {
        // Este método pode ser implementado com lógica mais avançada
        // de detecção de relacionamentos baseada no modelo
        return [];
    }

    /**
     * Obter relacionamentos para um contexto específico.
     */
    protected function getRelationsForContext(string $context): array
    {
        // Implementação básica - pode ser overridden em controllers filhos
        $model = $this->getModelInstance();
        
        // Se o modelo tem um método específico para relacionamentos
        if (method_exists($model, 'getRelationsFor' . ucfirst($context))) {
            $method = 'getRelationsFor' . ucfirst($context);
            return $model->{$method}();
        }
        
        // Relacionamentos padrão baseados no contexto
        switch ($context) {
            case 'index':
                return $this->getIndexRelations();
            case 'show':
            case 'edit':
                return $this->getDetailRelations();
            default:
                return [];
        }
    }

    /**
     * Relacionamentos para a página index.
     */
    protected function getIndexRelations(): array
    {
        return []; // Override em controllers filhos
    }

    /**
     * Relacionamentos para páginas de detalhes.
     */
    protected function getDetailRelations(): array
    {
        return []; // Override em controllers filhos
    }

    /**
     * Verificar se o modelo tem uma coluna específica.
     */
    protected function modelHasColumn(string $column): bool
    {
        try {
            $model = $this->getModelInstance();
            return method_exists($model, 'hasColumn') ? 
                $model->hasColumn($column) : 
                in_array($column, $model->getFillable());
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Obter a tabela (table instance) - método básico.
     * Este método deve ser implementado em controllers que usam Table.
     */
    protected function getTable()
    {
        // Se existe um método específico no controller filho, usar ele
        if (method_exists($this, 'table')) {
            return $this->table();
        }

        // Fallback: retornar null (métodos que dependem disso devem verificar)
        return null;
    }

    /**
     * Verificar se uma permissão pode ser executada (compatibility method).
     */
    protected function checkPermission(string $permission, $model = null): bool
    {
        // Se não há sistema de permissões, permitir por padrão
        if (!auth()->check()) {
            return false;
        }

        // Implementação básica - pode ser overridden em controllers filhos
        try {
            if ($model && is_string($model)) {
                // É uma classe de modelo
                return auth()->user()->can($permission, $model);
            } elseif ($model) {
                // É uma instância de modelo
                return auth()->user()->can($permission, $model);
            } else {
                // Permissão geral
                return auth()->user()->can($permission);
            }
        } catch (\Exception $e) {
            // Se falhar, permitir por padrão (manter compatibilidade)
            return true;
        }
    }

    /**
     * Authorizar uma permissão específica (compatibility method).
     */
    protected function authorizePermission(string $permission, $model = null): void
    {
        // Se não há sistema de permissões configurado, apenas retornar
        if (!method_exists($this, 'shouldCheckPermissions') || !$this->shouldCheckPermissions()) {
            return;
        }

        try {
            if ($model && is_string($model)) {
                // É uma classe de modelo
                $this->authorize($permission, $model);
            } elseif ($model) {
                // É uma instância de modelo
                $this->authorize($permission, $model);
            } else {
                // Permissão geral
                $this->authorize($permission);
            }
        } catch (\Exception $e) {
            // Se falhar na autorização, lançar exceção apropriada
            throw new \Illuminate\Auth\Access\AuthorizationException('Acesso negado: ' . $permission);
        }
    }
}
