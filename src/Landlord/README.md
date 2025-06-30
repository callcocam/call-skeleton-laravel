# Landlord Multi-Tenant System

Sistema de multi-tenancy que permite isolamento de dados por tenant, com bypass automático para usuários landlord (administradores).

## Configuração

### Arquivo: `config/tenant.php`

```php
return [
    'models' => [
        'tenant' => \VendorName\Skeleton\Models\Tenant::class,
    ],
    
    'tenant' => [
        'column' => 'tenant_id',
        'auto_scope' => true,
        'strict_mode' => true,
    ],
    
    // Colunas padrão usadas para scoping quando não especificadas no modelo
    'default_tenant_columns' => ['tenant_id'],
    
    'domain' => [
        'enabled' => true,
        'subdomain_enabled' => true,
        'cache_ttl' => 3600,
    ],
];
```

### Arquivo: `config/react-papa-leguas.php`

```php
return [
    'landlord' => [
        'model' => \VendorName\Skeleton\Models\Admin::class,
        'table' => 'admins',
        'routes' => [
            'prefix' => 'landlord',
            'middleware' => ['web'],
            // ... outras configurações de rotas
        ],
        // ... outras configurações de landlord
    ],
    
    'models' => [
        'role' => \VendorName\Skeleton\Shinobi\Models\Role::class,
        'permission' => \VendorName\Skeleton\Shinobi\Models\Permission::class,
    ],
];
```

### Arquivo: `config/shinobi.php`

Configurações específicas do sistema de roles e permissões:

```php
return [
    'models' => [
        'role' => \VendorName\Skeleton\Shinobi\Models\Role::class,
        'permission' => \VendorName\Skeleton\Shinobi\Models\Permission::class,
    ],
    
    'tables' => [
        'roles' => 'roles',
        'permissions' => 'permissions',
        'role_user' => 'role_user',
        'permission_user' => 'permission_user',
        'permission_role' => 'permission_role',
    ],
    
    'cache' => [
        'enabled' => true,
        'tag' => 'shinobi.permissions',
        'length' => 60 * 24, // 24 horas em minutos
    ],
];
```

## Configurações Unificadas

O sistema agora usa uma configuração unificada onde:

- **Tenant scoping**: Use `config('tenant.*')` para configurações de tenant
- **Landlord auth**: Use `config('react-papa-leguas.landlord.*')` para autenticação landlord  
- **Models**: Use `config('react-papa-leguas.models.*')` ou `config('shinobi.models.*')` para modelos
- **Colunas padrão**: Use `config('tenant.default_tenant_columns')` para colunas de tenant

## Características

- **Auto-Scoping**: Filtragem automática por tenant em todos os modelos
- **Landlord Bypass**: Usuários do guard `landlord` têm acesso global
- **Domain/Subdomain Resolution**: Resolução automática de tenant por domínio
- **Flexible Control**: Métodos para controlar scoping dinamicamente

## Uso Básico

### 1. Adicionar trait aos modelos

```php
use VendorName\Skeleton\Landlord\BelongsToTenants;

class Post extends Model
{
    use BelongsToTenants;
    
    // O modelo será automaticamente filtrado por tenant_id
}
```

### 2. Configurar colunas de tenant personalizadas

```php
class Post extends Model
{
    use BelongsToTenants;
    
    protected $tenantColumns = ['tenant_id', 'company_id'];
}
```

## Bypass para Landlords

### Automático
- Usuários autenticados via guard `landlord` têm acesso global
- Rotas com prefixo `/landlord/*` ignoram tenant scoping

### Manual - Desabilitar temporariamente

```php
use VendorName\Skeleton\Landlord\TenantManager;

// Para uma operação específica
app(TenantManager::class)->withoutTenantScoping(function () {
    return Post::all(); // Retorna posts de todos os tenants
});

// Ou usar middleware
Route::middleware('disable.tenant.scoping')->group(function () {
    // Rotas sem tenant scoping
});
```

### Manual - Forçar scoping para landlord

```php
// Quando landlord precisa trabalhar no contexto de um tenant específico
app(TenantManager::class)->withTenantScoping(function () {
    return Post::all(); // Respeitará o tenant atual
});
```

## Controle Dinâmico

```php
$tenantManager = app(TenantManager::class);

// Desabilitar scoping globalmente
$tenantManager->disable();

// Habilitar scoping
$tenantManager->enable();

// Adicionar tenant específico
$tenantManager->addTenant('tenant_id', 123);

// Remover tenant
$tenantManager->removeTenant('tenant_id');

// Verificar se tenant está ativo
if ($tenantManager->hasTenant('tenant_id')) {
    // ...
}
```

## Configuração de Tenant

O sistema resolve automaticamente o tenant por:

1. **Domínio exato**: `empresa1.com` → tenant com domain = 'empresa1.com'
2. **Subdomínio**: `empresa1.app.com` → tenant com prefix = 'empresa1'

## Exemplos de Uso

### Controller Landlord
```php
class LandlordDashboardController extends Controller
{
    public function stats()
    {
        // Automaticamente sem scoping - vê todos os tenants
        $totalUsers = User::count();
        $totalPosts = Post::count();
        
        return view('landlord.stats', compact('totalUsers', 'totalPosts'));
    }
    
    public function tenantDetails($tenantId)
    {
        // Forçar contexto de tenant específico
        return app(TenantManager::class)->withTenantScoping(function () use ($tenantId) {
            app(TenantManager::class)->addTenant('tenant_id', $tenantId);
            return Post::with('user')->get();
        });
    }
}
```

### Model com bypass condicional
```php
class Post extends Model
{
    use BelongsToTenants;
    
    public function scopeGlobal($query)
    {
        // Scope que sempre ignora tenant
        return app(TenantManager::class)->withoutTenantScoping(function () use ($query) {
            return $query;
        });
    }
}

// Uso
$globalPosts = Post::global()->get(); // Posts de todos os tenants
$tenantPosts = Post::all(); // Posts do tenant atual (se houver)
```

## Middleware Disponível

- `landlord.auth`: Autentica usuário via guard landlord
- `disable.tenant.scoping`: Desabilita tenant scoping para a requisição

## Configuração

Veja `config/tenant.php` e `config/react-papa-leguas.php` para opções de configuração.