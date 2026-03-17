# Migração: plannerate → laravel-raptor-planogram

Documento de acompanhamento da extração do editor de planograma do app `plannerate`
para este pacote. Seguir as fases em ordem — cada fase é independente e pode ser
validada antes de avançar.

**Branch de trabalho:** `feature/extract-planogram-editor`  
**Referência de arquitetura:** `packages/callcocam/laravel-raptor-flow/`

### Decisões fundamentais

- **Legado mantido em paralelo** — nenhum arquivo do app é removido até que toda a migração esteja concluída e validada em staging. O app e o pacote coexistem; o pacote substitui gradualmente.
- **Nomenclatura pode evoluir** — nomes de arquivos, pastas e classes podem ser renomeados para maior clareza durante a migração. Se uma renomeação for feita, registrar aqui o mapeamento `origem → destino` para rastrear.

---

## Mapeamento origem → destino

Tabela de rastreamento de todos os arquivos movidos/renomeados.
Atualizar à medida que cada item for migrado.

### Backend PHP

| Arquivo original (app/) | Destino no pacote (src/) | Renomeado? | Status |
|---|---|---|---|
| `Models/Editor/Planogram.php` | `Models/Planogram.php` | não | ✅ |
| `Models/Editor/Gondola.php` | `Models/Gondola.php` | não | ✅ |
| `Models/Editor/Section.php` | `Models/Section.php` | não | ✅ |
| `Models/Editor/Shelf.php` | `Models/Shelf.php` | não | ✅ |
| `Models/Editor/Segment.php` | `Models/Segment.php` | não | ✅ |
| `Models/Editor/Layer.php` | `Models/Layer.php` | não | ✅ |
| `Services/Plannerate/PlanogramChangeService.php` | `Services/PlanogramChangeService.php` | não | ✅ |
| `Services/Plannerate/GondolaService.php` | `Services/GondolaService.php` | não | ✅ |
| `Services/Plannerate/GondolaPayloadService.php` | `Services/GondolaPayloadService.php` | não | ✅ |
| `Services/Plannerate/SectionService.php` | `Services/SectionService.php` | não | ✅ |
| `Services/Plannerate/ShelfService.php` | `Services/ShelfService.php` | não | ✅ |
| `Services/Plannerate/ShelfPositioningService.php` | `Services/ShelfPositioningService.php` | não | ✅ |
| `Services/Plannerate/SegmentService.php` | `Services/SegmentService.php` | não | ✅ |
| `Services/Plannerate/LayerService.php` | `Services/LayerService.php` | não | ✅ |
| `Services/Plannerate/ProductService.php` | `Services/ProductService.php` | não | ✅ |
| `Services/Plannerate/AbcAnalysisService.php` | `Services/Analysis/AbcAnalysisService.php` | ✏️ pasta | ✅ |
| `Services/Plannerate/TargetStockService.php` | `Services/Analysis/TargetStockService.php` | ✏️ pasta | ✅ |
| `Services/Plannerate/AutoGenerate/*` | `Services/AutoGenerate/*` | não | ✅ |
| `Services/Plannerate/IAGenerate/*` | `Services/AiGenerate/*` | ✏️ `IA→Ai` | ✅ |
| `Services/Plannerate/SectionGenerate/*` | `Services/SectionGenerate/*` | não | ✅ |
| `Services/Printing/GondolaPrintService.php` | `Services/Printing/GondolaPrintService.php` | não | ✅ |
| `Services/QRCode/QRCodeService.php` | `Services/QRCode/QRCodeService.php` | não | ✅ |
| `Http/Controllers/Tenant/Plannerate/Editor/PlannerateController.php` | `Http/Controllers/PlanogramEditorController.php` | ✏️ nome mais claro | ✅ |
| `Http/Controllers/Tenant/Plannerate/Editor/PlanogramApiController.php` | `Http/Controllers/Editor/PlanogramApiController.php` | não | ✅ |
| `Http/Controllers/Tenant/Plannerate/Editor/GondolaController.php` | `Http/Controllers/Editor/GondolaController.php` | não | ✅ |
| `Http/Controllers/Tenant/Plannerate/Editor/SectionController.php` | `Http/Controllers/Editor/SectionController.php` | não | ✅ |
| `Http/Controllers/Tenant/Plannerate/Editor/ShelfController.php` | `Http/Controllers/Editor/ShelfController.php` | não | ✅ |
| `Http/Controllers/Tenant/Plannerate/Editor/SegmentController.php` | `Http/Controllers/Editor/SegmentController.php` | não | ✅ |
| `Http/Controllers/Tenant/Plannerate/Editor/LayerController.php` | `Http/Controllers/Editor/LayerController.php` | não | ✅ |
| `Http/Controllers/Tenant/Plannerate/Editor/SaveChangesController.php` | `Http/Controllers/Editor/SaveChangesController.php` | não | ✅ |
| `Http/Controllers/Tenant/Plannerate/Editor/CategoryController.php` | `Http/Controllers/Editor/CategoryController.php` | não | ✅ |
| `Http/Controllers/Tenant/Plannerate/Editor/ProductDimensionController.php` | `Http/Controllers/Editor/ProductDimensionController.php` | não | ✅ |
| `Http/Controllers/Tenant/Plannerate/Editor/ProductSalesController.php` | `Http/Controllers/Editor/ProductSalesController.php` | não | ✅ |
| `Http/Controllers/Tenant/Plannerate/GondolaPdfPreviewController.php` | `Http/Controllers/GondolaPdfPreviewController.php` | não | ✅ |
| `Http/Controllers/Tenant/Plannerate/AutoPlanogramController.php` | `Http/Controllers/AutoPlanogramController.php` | não | ✅ |
| `Http/Requests/Tenant/Plannerate/Editor/*` | `Http/Requests/Editor/*` | não | ✅ |
| `Enums/GondolaWorkflowStatus.php` | `Enums/GondolaWorkflowStatus.php` | não | ✅ |
| `DTOs/Plannerate/*` | `DTOs/*` | não | ✅ |

### Frontend TS/Vue

| Arquivo original (resources/js/) | Destino no pacote (resources/js/) | Renomeado? | Status |
|---|---|---|---|
| `types/planogram.ts` | `types/planogram.ts` | não | ✅ |
| `composables/plannerate/v3/usePlanogramEditor.ts` | `composables/plannerate/v3/usePlanogramEditor.ts` | não | 🔄 |
| `composables/plannerate/v3/usePlanogramChanges.ts` | `composables/plannerate/v3/usePlanogramChanges.ts` | não | 🔄 |
| `composables/plannerate/v3/usePlanogramHistory.ts` | `composables/plannerate/v3/usePlanogramHistory.ts` | não | 🔄 |
| `composables/plannerate/v3/usePlanogramSelection.ts` | `composables/plannerate/v3/usePlanogramSelection.ts` | não | 🔄 |
| `composables/plannerate/v3/usePlanogramKeyboard.ts` | `composables/plannerate/v3/usePlanogramKeyboard.ts` | não | 🔄 |
| `composables/plannerate/v3/useGondolaFields.ts` | `composables/plannerate/v3/useGondolaFields.ts` | não | ✅ |
| `composables/plannerate/v3/useSectionFields.ts` | `composables/plannerate/v3/useSectionFields.ts` | não | ✅ |
| `composables/plannerate/v3/useShelfFields.ts` | `composables/plannerate/v3/useShelfFields.ts` | não | ✅ |
| `composables/plannerate/v3/editor/*` | `composables/plannerate/v3/editor/*` | não | ✅ |
| `composables/plannerate/v3/use*.ts` (demais) | `composables/plannerate/v3/use*.ts` | não | ✅ |
| `composables/plannerate/analysis/useAnalysisFilters.ts` | `composables/plannerate/analysis/useAnalysisFilters.ts` | não | ✅ |
| `composables/plannerate/useArrayNavigation.ts` | `composables/plannerate/useArrayNavigation.ts` | não | ✅ |
| `composables/plannerate/usePdfGenerator.ts` | `composables/plannerate/usePdfGenerator.ts` | não | ✅ |
| `composables/plannerate/useShelfAreaCalculation.ts` | `composables/plannerate/useShelfAreaCalculation.ts` | não | ✅ |
| `composables/plannerate/v3/usePlanogramUtils.ts` | `composables/plannerate/v3/usePlanogramUtils.ts` | não | ✅ |
| `components/plannerate/v3/**` | `components/plannerate/v3/**` | não | ✅ |
| `components/plannerate/client/**` | `components/plannerate/client/**` | não | ✅ |
| `components/plannerate/print/**` | `components/plannerate/print/**` | não | ✅ |
| `components/plannerate/analysis/**` | `components/plannerate/analysis/**` | não | ✅ |

> Legenda: ⬜ pendente · 🔄 em progresso · ✅ concluído · ✏️ renomeado

---

## Estado atual do pacote

```
src/
  Commands/LaravelRaptorPlanogramCommand.php   ← gerado pelo scaffold
  Facades/LaravelRaptorPlanogram.php           ← gerado pelo scaffold
  LaravelRaptorPlanogram.php                   ← gerado pelo scaffold
  LaravelRaptorPlanogramServiceProvider.php    ← config + rotas base registradas
config/
  plannogram.php
routes/
  planogram.php
resources/
  js/components/plannerate/PackageWorkbench.vue
  js/composables/plannerate/analysis/useAnalysisFilters.ts
  js/composables/plannerate/useArrayNavigation.ts
  js/composables/plannerate/useShelfAreaCalculation.ts
  js/composables/plannerate/v3/usePlanogramChanges.ts
  js/composables/plannerate/v3/usePlanogramHistory.ts
  js/composables/plannerate/v3/usePlanogramSelection.ts
  js/composables/plannerate/v3/usePlanogramKeyboard.ts
  js/composables/plannerate/v3/usePlanogramEditor.ts
  js/composables/plannerate/v3/useGondolaFields.ts
  js/composables/plannerate/v3/useSectionFields.ts
  js/composables/plannerate/v3/useShelfFields.ts
  js/composables/plannerate/v3/usePlanogramUtils.ts
  js/pages/package/planogram/workbench.vue
  js/types/planogram.ts
  views/.gitkeep
```

> O `composer.json` está configurado com namespace `Callcocam\LaravelRaptorPlanogram\`
> e registrado no `composer.json` root via `repositories` path.
> O app já está com o pacote em `require` e a descoberta do provider está sendo
> feita automaticamente pelo Composer/Laravel.

---

## Fase 1 — Scaffold completo do pacote

**Objetivo:** criar toda a estrutura de pastas, `ServiceProvider` funcional,
config e registrar o pacote no app.

- [x] Adicionar `"callcocam/laravel-raptor-planogram": "dev-main"` ao `require` do `composer.json` root
- [x] Adicionar `"callcocam/laravel-raptor": "dev-main"` como dependência no `composer.json` do pacote
- [x] Executar `./vendor/bin/sail composer update` / `composer require` para registrar o pacote no app
- [x] Confirmar descoberta automática do `LaravelRaptorPlanogramServiceProvider` via Composer
- [x] Criar `config/plannogram.php` no pacote com modelos e rotas configuráveis
- [x] Publicar/registrar config no `ServiceProvider` (`hasConfigFile('plannogram')`)
- [ ] Criar estrutura de pastas `src/`:
  ```
  src/
    Contracts/
    DTOs/
    Enums/
    Http/
      Controllers/
        Editor/
      Requests/
        Editor/
    Models/
    Policies/
    Services/
      AutoGenerate/
      IAGenerate/
      SectionGenerate/
      Printing/
      QRCode/
    Support/
      Traits/
  ```
- [x] Criar `routes/planogram.php` base no pacote
- [ ] Criar `database/migrations/` com as migrations dos modelos do pacote
- [x] Validar: Composer/Laravel descobrem o pacote local com sucesso

### Entrega inicial concluída nesta fase

- [x] Alias Vite `@planogram` configurado no app
- [x] Resolução Inertia no app e SSR configurada para páginas do pacote
- [x] Página isolada de teste criada: rota `planogram-package-test`
- [x] Primeiro lote de composables migrado: fields + utils + types
- [x] Segundo lote migrado: navegação, área de prateleira e filtros de análise
- [x] Terceiro lote copiado: orquestração (changes/history/selection/keyboard/editor) em modo compatível com legado
- [x] Quarto lote migrado: helpers de `v3/editor` (state/lookups/reatividade/operações/drag-drop)
- [x] Quinto lote migrado: `useSectionHoles.ts` e desacoplamento direto em `usePlanogramEditor.ts`
- [x] Sexto lote migrado: `v3/use*.ts` restantes (ABC, performance, produto, ações e target stock)
- [x] Sétimo lote migrado: `usePdfGenerator.ts`
- [x] Oitavo lote migrado: componentes `v3` iniciais (top-level + `editor/*`)
- [x] Nono lote migrado: componentes `v3/form/*` e `v3/form/steps/*`
- [x] Décimo lote migrado: componentes `v3/header/*` e `v3/header/partials/*`
- [x] Décimo primeiro lote migrado: componentes `v3/sidebar/products/*` e `v3/sidebar/properties/*`
- [x] Décimo segundo lote migrado: componentes `client/*`
- [x] Décimo terceiro lote migrado: componentes `print/*` e `print/partials/*`
- [x] Décimo quarto lote migrado: componentes `analysis/*`, `analysis/abc/*` e `analysis/target-stock/*`
- [x] Décimo quinto lote: padronização de nomenclatura em inglês (arquivos-chave em `v3`, `client`, `print` e docs locais)
- [x] Décimo sexto lote: padronização interna em inglês (aliases/imports/tags e variáveis internas nos componentes)
- [x] Décimo sétimo lote: migração inicial de modelos PHP (`Planogram` e `Gondola`) para `src/Models`
- [x] Décimo oitavo lote: migração de modelos PHP (`Section` e `Shelf`) para `src/Models`
- [x] Décimo nono lote: migração de modelos PHP (`Segment` e `Layer`) para `src/Models`
- [x] Vigésimo lote: trait `HasCrossDatabaseRelations` + contratos base de integração (`Product`, `Category`, `Sale`, `MonthlySalesSummary`)
- [x] Vigésimo primeiro lote: bindings de contratos + `class_alias` de retrocompatibilidade no `AppServiceProvider`
- [x] Vigésimo segundo lote: migração inicial de services (`PlanogramChangeService` e `GondolaService`) para `src/Services`
- [x] Vigésimo terceiro lote: migração de services core (`SectionService`, `ShelfService`, `SegmentService`, `LayerService` e `ProductService`) para `src/Services`
- [x] Vigésimo quarto lote: migração de services de suporte (`GondolaPayloadService` e `ShelfPositioningService`) para `src/Services`
- [x] Vigésimo quinto lote: migração de services de análise (`AbcAnalysisService` e `TargetStockService`) para `src/Services/Analysis` em modo compatibilidade
- [x] Vigésimo sexto lote: migração de `Services/Plannerate/AutoGenerate/*` para `src/Services/AutoGenerate` em modo compatibilidade
- [x] Vigésimo sétimo lote: migração de `Services/Plannerate/IAGenerate/*` para `src/Services/AiGenerate` em modo compatibilidade
- [x] Vigésimo oitavo lote: migração de `Services/Plannerate/SectionGenerate/*` para `src/Services/SectionGenerate` em modo compatibilidade
- [x] Vigésimo nono lote: migração de `Services/Printing/GondolaPrintService.php` e `Services/QRCode/QRCodeService.php` para `src/Services`
- [x] Trigésimo lote: registro de singletons dos services migrados no `LaravelRaptorPlanogramServiceProvider`
- [x] Trigésimo primeiro lote: migração de `Enums/GondolaWorkflowStatus.php` para `src/Enums/GondolaWorkflowStatus.php`
- [x] Trigésimo segundo lote: migração de `Http/Requests/Tenant/Plannerate/Editor/*` para `src/Http/Requests/Editor/*` + testes focados
- [x] Trigésimo terceiro lote: migração de `DTOs/Plannerate/*` para `src/DTOs/*` + atualização de imports em `src/Services/*` + testes focados
- [x] Trigésimo quarto lote: migração de controllers da Fase 5 + rotas de `routes/editor.php` para `routes/planogram.php` + smoke test de autoload
- [x] Trigésimo quinto lote: migração das rotas `export/gondola/*` para `routes/export.php` no pacote + shim de compatibilidade em `routes/export.php` do app
- [x] Trigésimo sexto lote: `routes/editor.php` do app convertido para shim do pacote (`routes/planogram.php`) + regeneração de Wayfinder
- [x] Trigésimo sétimo lote: hardening de controllers migrados (troca de models do domínio planograma de `App\Models\Editor` para `src/Models` no pacote)
- [x] Trigésimo oitavo lote: hardening contratual de controllers para `Category` e `Product` via `PlanogramCategoryContract` / `PlanogramProductContract` (compatibilidade mantida para `Sale` e `GondolaAnalysis`)
- [x] Trigésimo nono lote: criação dos adapters `app/Repositories/PlanogramSaleRepository.php` e `app/Repositories/PlanogramSalesSummaryRepository.php` + bindings no `AppServiceProvider` + migração de `AbcAnalysisService` e `TargetStockService` para `PlanogramSaleRepositoryContract` / `PlanogramSalesSummaryRepositoryContract` + teste focado de bindings
- [x] Quadragésimo lote: hardening de `ProductSalesController` para remoção de dependência direta de `App\Models\Sale`, passando a consumir `PlanogramSaleRepositoryContract` via DI
- [x] Quadragésimo primeiro lote: hardening de `Planogram` para usar enum do pacote (`Callcocam\LaravelRaptorPlanogram\Enums\GondolaWorkflowStatus`) no lugar de `App\Enums\GondolaWorkflowStatus`
- [x] Quadragésimo segundo lote: criação de `PlanogramGondolaAnalysisRepositoryContract` + adapter `app/Repositories/PlanogramGondolaAnalysisRepository.php` e hardening de `GondolaController`/`GondolaPdfPreviewController` para remover dependência direta de `App\Models\GondolaAnalysis`
- [x] Quadragésimo terceiro lote: criação de `PlanogramWorkflowContract`/`GondolaWorkflowContract` + bindings no `AppServiceProvider` + hardening de `Planogram`, `Gondola`, `PlanogramEditorController` e `GondolaController` para remover dependência direta de `App\Models\Workflow\*`
- [x] Quadragésimo quarto lote: criação de `PlanogramKanbanServiceContract` + `implements` em `App\Services\Workflow\KanbanService` + binding no `AppServiceProvider` + hardening de `PlanogramEditorController` para remover dependência direta de `App\Services\Workflow\KanbanService`
- [x] Quadragésimo quinto lote: hardening de modelos `Planogram` e `Layer` para remover dependências diretas de `App\Models\Editor\Category`, `App\Models\Editor\Product` e `App\Models\Traits\HasCategory`, usando `PlanogramCategoryContract` / `PlanogramProductContract` + teste focado de bindings
- [x] Quadragésimo sexto lote: criação de `PlanogramStoreRepositoryContract` e `PlanogramUserRepositoryContract` + adapters `app/Repositories/PlanogramStoreRepository.php` / `app/Repositories/PlanogramUserRepository.php` + bindings no `AppServiceProvider` + hardening de `PlanogramEditorController` para remover dependências diretas de `App\Models\Store` e `App\Models\User`
- [x] Quadragésimo sétimo lote: criação de `PlanogramProductImageDispatcherContract` + adapter `app/Repositories/PlanogramProductImageDispatcher.php` + binding no `AppServiceProvider` + hardening de `GondolaController::updateImages` para remover dependência direta de `App\Jobs\ProcessProductImagesByEansJob`
- [x] Quadragésimo oitavo lote: migração de `AutoGeneratePlanogramRequest` e `IAGeneratePlanogramRequest` para `src/Http/Requests/*` no pacote + hardening de `AutoPlanogramController` para remover dependência direta de `App\Http\Requests\Tenant\Plannerate\*` + testes focados de validação
- [x] Quadragésimo nono lote: hardening de `RankedProductDTO` para remover dependência direta de `App\Models\Product` (tipo concreto), mantendo compatibilidade por tipagem genérica de objeto
- [x] Quinquagésimo lote: ampliação de `PlanogramUserRepositoryContract` com busca por tenant + hardening de `GondolaController::getAvailableUsers` para remover dependência direta de `App\Models\User`
- [x] Quinquagésimo primeiro lote: hardening de `AbcAnalysisService` e `TargetStockService` para remover dependências diretas de `App\Models\Editor\Product`/`Category`, resolvendo model class via `PlanogramProductContract`
- [x] Quinquagésimo segundo lote: hardening de `GondolaPayloadService` para remover dependência direta de `App\Models\Store`, reutilizando `PlanogramStoreRepositoryContract`
- [x] Quinquagésimo terceiro lote: hardening de `ProductSelectionService` e `AutoPlanogramService` para remover dependências diretas de `App\Models\Planogram`/`Product`, usando `Planogram` do pacote e resolução de produto via `PlanogramProductContract`
- [x] Quinquagésimo quarto lote: hardening de `IAPlanogramService` para remover dependências diretas de `App\Models\Editor\Category`/`Product`, resolvendo model class via `PlanogramCategoryContract` / `PlanogramProductContract`
- [x] Quinquagésimo quinto lote: hardening de `SectionGenerate/*` (`SectionAIAllocator`, `SectionContextBuilder`, `SectionPersistenceService`, `SectionRulesAllocator`) para usar `Section` do pacote no lugar de `App\Models\Editor\Section`
- [x] Quinquagésimo sexto lote: criação de `PlanogramTenantConnectionResolverContract` + adapter `app/Services/PlanogramTenantConnectionResolver.php` + binding no `AppServiceProvider` + hardening de `SectionPlanogramService` para remover dependências diretas de `App\Concerns\BelongsToConnection`, `App\Models\Client` e `App\Models\Planogram`
- [x] Quinquagésimo sétimo lote: hardening de `AutoPlanogramService` e `IAPlanogramService` para usar `PlanogramTenantConnectionResolverContract` no setup de conexão tenant, removendo dependências diretas de `App\Concerns\BelongsToConnection` e `App\Models\Client`
- [x] Quinquagésimo oitavo lote: criação de `PlanogramGondolaRepositoryContract` e `PlanogramSectionRepositoryContract` + bindings no `AppServiceProvider` + hardening de `GondolaService`, `SectionService` e `PlanogramChangeService` para remover dependências diretas de `App\Repositories\Plannerate\GondolaRepository`/`SectionRepository`
- [x] Quinquagésimo nono lote: criação de `PlanogramShelfRepositoryContract`, `PlanogramSegmentRepositoryContract`, `PlanogramLayerRepositoryContract` e `PlanogramProductRepositoryContract` + bindings no `AppServiceProvider` + hardening de `ShelfService`, `SegmentService`, `LayerService` e `ProductService` para remover dependências diretas de `App\Repositories\Plannerate\ShelfRepository`/`SegmentRepository`/`LayerRepository`/`ProductRepository`
- [x] Sexagésimo lote: criação de `PlanogramSectionAllocatorContract` + binding no `AppServiceProvider` + hardening de `SectionAIAllocator` para remover dependência direta de `App\Ai\Agents\PlanogramSectionAllocator`
- [x] Sexagésimo primeiro lote: desacoplamento de rotas do pacote (`routes/planogram.php` e `routes/export.php`) para controllers externos via `config('plannogram.controllers.*')` + wiring das classes concretas no `AppServiceProvider`, removendo referências diretas de controllers do app nas rotas do pacote
- [x] Sexagésimo segundo lote: desacoplamento de configurações indiretas do app no runtime do pacote, trocando `config('app.current_client_id')`/`config('app.url')` por `config('plannogram.current_client_id')`/`config('plannogram.app_url')` + wiring desses valores no `AppServiceProvider`
- [x] Sexagésimo terceiro lote: desacoplamento de leitura direta de `config('database.connections.tenant.database')` no runtime do pacote, migrando para `config('plannogram.tenant_database')` + wiring no `AppServiceProvider`
- [x] Sexagésimo quarto lote (varredura final — backend): confirmação de que o backend PHP do pacote está zero-acoplado ao app host. `config('raptor.database.landlord_connection_name')` é dependência inter-pacote aceitável (com fallback); `auth()` é helper padrão Laravel; tabelas `stores`/`clients`/`clusters` no `HasCrossDatabaseRelations` são do schema landlord Raptor. Nenhuma ação corretiva necessária no backend PHP. Acoplamento JS via `@/components/ui/*` e `@/components/plannerate/v3/*` documentado como escopo separado (Gargalho #6 — frontend migration lot).
- [x] Build frontend validado com assets do pacote
- [x] Teste automatizado da página isolada criado e passando

---

## Fase 2 — Modelos PHP

**Objetivo:** mover os 6 modelos do domínio central para o pacote, mantendo
os mesmos nomes de tabela. Criar contratos para os modelos que ficam no app.

**Modelos que vão para o pacote** (`src/Models/`):

- [x] `Planogram` — origin: `app/Models/Editor/Planogram.php`
- [x] `Gondola` — origin: `app/Models/Editor/Gondola.php`
- [x] `Section` — origin: `app/Models/Editor/Section.php`
- [x] `Shelf` — origin: `app/Models/Editor/Shelf.php`
- [x] `Segment` — origin: `app/Models/Editor/Segment.php`
- [x] `Layer` — origin: `app/Models/Editor/Layer.php`

> Todos devem ter `protected $table = 'nome_da_tabela'` explícito para segurança.
> Todos estendem `Callcocam\LaravelRaptor\Models\AbstractModel`.

**Trait compartilhado:**

- [x] Criar `src/Support/Traits/HasCrossDatabaseRelations.php` — centraliza
  `getClientAttribute()`, `getStoreAttribute()`, `getClusterAttribute()` que hoje
  se repetem em `Planogram`, `Gondola`, `Sale` etc. usando
  `DB::connection('landlord')->table(...)`

**Contratos (models que ficam no app):**

- [x] `src/Contracts/PlanogramProductContract.php` — interface para `Product`
- [x] `src/Contracts/PlanogramCategoryContract.php` — interface para `Category`
- [x] `src/Contracts/PlanogramSaleRepositoryContract.php` — interface para acesso a `Sale`
- [x] `src/Contracts/PlanogramSalesSummaryRepositoryContract.php` — interface para `MonthlySalesSummary`

**No app (`app/Providers/AppServiceProvider.php` ou provider dedicado):**

- [x] Registrar bindings:
  ```php
  $this->app->bind(PlanogramProductContract::class, \App\Models\Editor\Product::class);
  $this->app->bind(PlanogramCategoryContract::class, \App\Models\Editor\Category::class);
  ```

- [x] Registrar adapters concretos para repositórios de vendas:
  ```php
  $this->app->bind(PlanogramSaleRepositoryContract::class, \App\Repositories\PlanogramSaleRepository::class);
  $this->app->bind(PlanogramSalesSummaryRepositoryContract::class, \App\Repositories\PlanogramSalesSummaryRepository::class);
  ```

**Aliases de retrocompatibilidade** (durante a transição, no `AppServiceProvider`):

- [x] `class_alias(\Callcocam\LaravelRaptorPlanogram\Models\Planogram::class, \App\Models\Editor\Planogram::class)`
- [x] Idem para `Gondola`, `Section`, `Shelf`, `Segment`, `Layer`

- [ ] Validar: `./vendor/bin/sail artisan tinker` → instanciar `Planogram::first()`
  - Observação: tentativa executada em 2026-03-17, bloqueada por tabela `planograms` ausente no banco local (`relation "planograms" does not exist`).

---

## Fase 3 — Services

**Objetivo:** mover toda a lógica de negócio para `src/Services/`.
Os services recebem contratos por DI — nenhuma referência a `App\Models`.

**Core do editor:**

- [x] `PlanogramChangeService` — origin: `app/Services/Plannerate/PlanogramChangeService.php`
- [x] `GondolaService` — origin: `app/Services/Plannerate/GondolaService.php`
- [x] `GondolaPayloadService` — origin: `app/Services/Plannerate/GondolaPayloadService.php`
- [x] `SectionService` — origin: `app/Services/Plannerate/SectionService.php`
- [x] `ShelfService` — origin: `app/Services/Plannerate/ShelfService.php`
- [x] `ShelfPositioningService` — origin: `app/Services/Plannerate/ShelfPositioningService.php`
- [x] `SegmentService` — origin: `app/Services/Plannerate/SegmentService.php`
- [x] `LayerService` — origin: `app/Services/Plannerate/LayerService.php`
- [x] `ProductService` — origin: `app/Services/Plannerate/ProductService.php` (modo compatibilidade com `ProductRepository`; injeção de `PlanogramProductContract` pendente)

**Análise de performance:**

- [x] `AbcAnalysisService` — origin: `app/Services/Plannerate/AbcAnalysisService.php` (query roots de vendas migrados para `PlanogramSaleRepositoryContract` e `PlanogramSalesSummaryRepositoryContract`; compatibilidade ainda mantida para `Product`/`Category`)
- [x] `TargetStockService` — origin: `app/Services/Plannerate/TargetStockService.php` (query roots de vendas migrados para `PlanogramSaleRepositoryContract` e `PlanogramSalesSummaryRepositoryContract`; compatibilidade ainda mantida para `Product`)

**Auto-geração:**

- [x] `AutoGenerate/AutoPlanogramService`
- [x] `AutoGenerate/LayoutOptimizationService`
- [x] `AutoGenerate/MerchandisingRulesService`
- [x] `AutoGenerate/ProductSelectionService`

**IA:**

- [x] `IAGenerate/IAPlanogramService` — dependência opcional: `prism-php/prism` (adicionar em `suggest` do `composer.json`) | modo compatibilidade com DTOs e models do app
- [x] `IAGenerate/IAPromptBuilderService` | modo compatibilidade com DTOs do app
- [x] `IAGenerate/IAResponseParserService` | modo compatibilidade com DTOs do app

**Geração por seção:**

- [x] `SectionGenerate/SectionAIAllocator` | modo compatibilidade com DTOs/models do app
- [x] `SectionGenerate/SectionContextBuilder` | modo compatibilidade com DTOs/models do app
- [x] `SectionGenerate/SectionPersistenceService` | modo compatibilidade com DTOs/models do app
- [x] `SectionGenerate/SectionPlanogramService` | modo compatibilidade com DTOs/models do app
- [x] `SectionGenerate/SectionRulesAllocator` | modo compatibilidade com DTOs/models do app

**Impressão e QR:**

- [x] `Printing/GondolaPrintService` — dependência opcional: `barryvdh/laravel-dompdf` | modo compatibilidade com models/services do app
- [x] `QRCode/QRCodeService` — dependência: `endroid/qr-code`

**Registrar bindings no `ServiceProvider`:**

- [x] Cada service registrado no container via `$this->app->singleton(...)`

---

## Fase 4 — Enums, Form Requests e DTOs

**Objetivo:** mover os tipos de suporte para o pacote.

**Enums:**

- [x] `src/Enums/GondolaWorkflowStatus.php` — origin: `app/Enums/GondolaWorkflowStatus.php`
- [ ] Outros enums específicos do editor (verificar `app/Enums/`)

**Form Requests** (`src/Http/Requests/Editor/`):

- [x] `SaveChangesRequest` — origin: `app/Http/Requests/Tenant/Plannerate/Editor/SaveChangesRequest.php`
- [x] Demais requests do editor (verificar `app/Http/Requests/Tenant/Plannerate/Editor/`)

**DTOs:**

- [x] Mover DTOs relacionados ao planograma de `app/DTOs/` para `src/DTOs/`

---

## Fase 5 — Controllers e Routes

**Objetivo:** mover os 11 controllers e registrar as rotas pelo `ServiceProvider`.

**Controllers** (`src/Http/Controllers/Editor/`):

- [x] `PlanogramEditorController` (renomeado de `PlannerateController`) — página Inertia principal do editor
- [x] `PlanogramApiController` — listagem e gondolas
- [x] `GondolaController` — CRUD + sections + products + update-images
- [x] `SectionController` — CRUD + transfer
- [x] `ShelfController` — CRUD
- [x] `SegmentController` — update
- [x] `LayerController` — update + destroy
- [x] `SaveChangesController` — delta save (lógica crítica)
- [x] `CategoryController` — resolução via `PlanogramCategoryContract`
- [x] `ProductDimensionController`
- [x] `ProductSalesController`
- [x] `GondolaPdfPreviewController`
- [x] `AutoPlanogramController`

> Hardening atual: `CategoryController`, `ProductDimensionController`, `ProductSalesController` e partes de `GondolaController` já resolvem `Category`/`Product` via contratos do pacote. Permanecem em modo compatibilidade dependências de `Sale`, `GondolaAnalysis`, `Workflow` e `ProductImageController`.

**Controllers que ficam no app** (dependem de modelos externos):

- `GondolaAnalysisController` — ABC/target-stock usa Sales diretamente
- `ProductImageController` — storage config do app

**Melhoria no `SaveChangesController`:**

- [ ] Substituir a invalidação de cache por combinatória manual de keys por um
  evento `PlanogramProductsChanged` (melhor desacoplamento):
  ```php
  event(new PlanogramProductsChanged($gondola));
  // O app registra um listener que invalida o cache específico
  ```

**Routes** (`routes/planogram.php` no pacote):

- [x] Mover rotas do editor para o pacote (baseado em `routes/editor.php` do app)
- [x] Prefixo e middleware configuráveis via `config/plannogram.php`
- [x] `ServiceProvider` registra as rotas:
  ```php
  Route::middleware(config('plannogram.route_middleware'))
      ->prefix(config('plannogram.route_prefix'))
      ->group(__DIR__.'/../routes/planogram.php');
  ```
- [x] Manter `routes/editor.php` do app como shim (`require base_path(...)`) para evitar quebra de contrato
- [x] Regenerar Wayfinder: `./vendor/bin/sail artisan wayfinder:generate --with-form`

**Routes de Export compatíveis** (`routes/export.php`):

- [x] Migrar definição de `export/gondola/*` para `routes/export.php` no pacote
- [x] Manter `routes/export.php` do app como shim (`require base_path(...)`) para evitar quebra de URL/nome de rota

---

## Fase 6 — Frontend: Composables

**Objetivo:** mover os composables TypeScript para o pacote e configurar o alias
Vite no app.

**Estrutura no pacote:**

```
resources/js/
  composables/
    plannerate/
      v3/
        editor/     (useGondolaState, useLookupHelpers, useReactivityHelpers,
                     useSectionOperations, useSegmentOperations,
                     useShelfDragDrop, useShelfOperations)
        useAbcClassification.ts
        useGondolaFields.ts
        usePlanogramChanges.ts
        usePlanogramEditor.ts
        usePlanogramHistory.ts
        usePlanogramKeyboard.ts
        usePlanogramSelection.ts
        usePlanogramUtils.ts
        usePerformanceIndicators.ts
        useProductImage.ts
        useProductSales.ts
        useProductsPanel.ts
        useSectionActions.ts
        useSectionFields.ts
        useSectionHoles.ts
        useSegmentActions.ts
        useShelfActions.ts
        useShelfFields.ts
        useTargetStockAnalysis.ts
      analysis/
        useAnalysisFilters.ts
      useArrayNavigation.ts
      usePdfGenerator.ts
      useShelfAreaCalculation.ts
  types/
    planogram.d.ts    ← tipos TypeScript centralizados
```

- [ ] Copiar todos os composables de `resources/js/composables/plannerate/` → `packages/.../resources/js/composables/plannerate/`
- [ ] Adicionar alias Vite no `vite.config.ts` do app:
  ```ts
  '#plannogram': path.resolve(__dirname,
    'packages/callcocam/laravel-raptor-planogram/resources/js')
  ```
- [ ] Criar re-exports em `resources/js/composables/plannerate/` do app para manter imports `@/composables/plannerate/...` funcionando durante a transição:
  ```ts
  // resources/js/composables/plannerate/v3/usePlanogramEditor.ts
  export * from '#plannogram/composables/plannerate/v3/usePlanogramEditor'
  ```
- [ ] Validar: app compila sem erros com `./vendor/bin/sail npm run build`

---

## Fase 7 — Frontend: Components

**Objetivo:** mover os 80+ componentes Vue para o pacote.

**Estrutura no pacote** (`resources/js/components/plannerate/`):

```
v3/
  Canvas.vue, Planogram.vue, Indicador.vue
  DropdownActions.vue, DropdownPerformance.vue
  editor/       (Section, Shelf, Segment, Layer, Cremalheira, AbcBadge,
                 StockIndicator, ConfirmDeleteDialog, DuplicateSectionDialog)
  form/         (GondolaCreateStepper, GondolaEditForm, AddModuleSheet,
                 SectionShelfBulkUpdate, steps/Step1..Step6)
  header/       (Header, Toolbar, Performance, AutoGenerateModal,
                 ConfirmDeleteGondolaDialog, MapRegionSelectorModal,
                 ShareQRCodeModal, PerformanceAbcTab, PerformanceTargetStockTab,
                 partials/*)
  sidebar/
    products/   (PanelLeft, Listar, Card, Category, CategorySelect,
                 Filters, Search, Stats)
    properties/ (PanelRight, partials/*)
client/         (Section, Sections, Shelf, Shelves, Segment, Layer,
                Cremalheira, AbcBadge, StockIndicator, ProductDetailsModal)
print/          (PdfPreview, PdfModuleSelector, partials/*)
analysis/       (Abc, AbcParamsModal, AbcResultsList, AnalysisPeriodSelector,
                TableHeadAnalysis, TargetStockParamsModal,
                TargetStockResultsList, abc/*, target-stock/*)
GondolaCards.vue, PlanogramaTabs.vue, StoreMapViewer.vue
AbcAnalysis.vue, AbcFilters.vue
```

- [ ] Copiar todos os componentes de `resources/js/components/plannerate/` → `packages/.../resources/js/components/plannerate/`
- [ ] Substituir imports internos `@/composables/plannerate/` por `#plannogram/composables/plannerate/` (script de substituição em batch)
- [ ] Substituir imports internos `@/components/plannerate/` por `#plannogram/components/plannerate/`
- [ ] Atualizar imports nas pages (`resources/js/pages/`) do app para usar `#plannogram/components/plannerate/`
- [ ] Validar: `./vendor/bin/sail npm run build` sem erros
- [ ] Smoke manual: abrir editor, drag-drop, autosave, undo/redo

---

## Fase 8 — Hardening, validação e limpeza

**Objetivo:** validar paridade total com o legado antes de qualquer remoção.
O código original permanece intacto até esta fase estar 100% concluída.

**Critérios de aceite antes de limpar o legado:**

- [ ] Smoke completo em staging com tenant real: autosave delta, undo/redo, drag-drop, reload, multi-tenant
- [ ] Nenhuma regressão nos testes do pacote vs testes originais do app
- [ ] Performance de payload save-changes igual ou melhor (medir antes/depois)
- [ ] Revisão de isolamento de tenant (dois tenants simultâneos)

**Limpeza do app** (somente após critérios de aceite acima aprovados):

- [ ] Remover `app/Models/Editor/Planogram.php` → Gondola → Section → Shelf → Segment → Layer (aliases de retrocompatibilidade já ativados na Fase 2)
- [ ] Remover services já movidos de `app/Services/Plannerate/`
- [ ] Remover controllers já movidos de `app/Http/Controllers/Tenant/Plannerate/Editor/`
- [ ] Remover composables já movidos de `resources/js/composables/plannerate/` (re-exports removíveis)
- [ ] Remover componentes já movidos de `resources/js/components/plannerate/`
- [ ] Limpar `routes/editor.php` do app (apenas imports do pacote)
- [ ] Atualizar tabela de mapeamento origem → destino com status ✅ em todos os itens

**Testes no pacote** (`tests/`):

- [ ] `tests/Feature/SaveChangesTest.php` — delta save: criação, atualização, deleção, transação
- [ ] `tests/Feature/GondolaControllerTest.php` — CRUD básico
- [ ] `tests/Feature/PlanogramChangeServiceTest.php` — processamento de deltas
- [ ] `tests/Unit/AbcAnalysisServiceTest.php`
- [ ] `tests/Unit/TargetStockServiceTest.php`

**Outros:**

- [ ] Atualizar `README.md` do pacote com instruções de instalação e configuração
- [ ] Verificar isolamento multi-tenant: testes com dois tenants simultâneos
- [ ] Medir performance: comparar payload de save-changes antes vs depois
- [ ] Documentar contrato de extensão (como um novo projeto usa o pacote)

---

## Config de referência (`config/plannogram.php`)

```php
return [
    /*
    |--------------------------------------------------------------------------
    | Route configuration
    |--------------------------------------------------------------------------
    */
    'route_prefix'     => 'api',
    'route_middleware' => ['web', 'auth', 'verified'],
    'route_name_prefix' => 'api.',

    /*
    |--------------------------------------------------------------------------
    | Model bindings (override to use app models)
    |--------------------------------------------------------------------------
    */
    'models' => [
        'planogram' => \Callcocam\LaravelRaptorPlanogram\Models\Planogram::class,
        'gondola'   => \Callcocam\LaravelRaptorPlanogram\Models\Gondola::class,
        'section'   => \Callcocam\LaravelRaptorPlanogram\Models\Section::class,
        'shelf'     => \Callcocam\LaravelRaptorPlanogram\Models\Shelf::class,
        'segment'   => \Callcocam\LaravelRaptorPlanogram\Models\Segment::class,
        'layer'     => \Callcocam\LaravelRaptorPlanogram\Models\Layer::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | External model contracts (must be bound by the app)
    |--------------------------------------------------------------------------
    */
    'contracts' => [
        'product'        => null, // App\Models\Editor\Product::class
        'category'       => null, // App\Models\Editor\Category::class
        'sale_repository' => null, // App\Repositories\SaleRepository::class
    ],

    /*
    |--------------------------------------------------------------------------
    | Storage
    |--------------------------------------------------------------------------
    */
    'storage' => [
        'disk'   => env('PLANOGRAM_STORAGE_DISK', 'spaces'),
        'prefix' => 'planogramas/',
    ],

    /*
    |--------------------------------------------------------------------------
    | Optional features
    |--------------------------------------------------------------------------
    */
    'features' => [
        'ai_generation' => env('PLANOGRAM_AI_ENABLED', false),
        'pdf_preview'   => env('PLANOGRAM_PDF_ENABLED', true),
        'qr_code'       => env('PLANOGRAM_QR_ENABLED', true),
    ],
];
```

---

## Comandos úteis

```bash
# Rodar todos os testes do pacote
./vendor/bin/sail artisan test --compact --filter=Planogram

# Regenerar Wayfinder após mover controllers
./vendor/bin/sail artisan wayfinder:generate --with-form

# Verificar se o pacote carregou
./vendor/bin/sail artisan about

# Build frontend
./vendor/bin/sail npm run build

# Pint no pacote
./vendor/bin/sail vendor/bin/pint packages/callcocam/laravel-raptor-planogram/src --dirty
```

---

## Gargalos conhecidos

| # | Problema | Solução |
|---|---|---|
| 1 | `SaveChangesController` invalida cache por combinatória manual de keys | Emitir evento `PlanogramProductsChanged` → app registra listener |
| 2 | `AbcAnalysisService` acessa `Sale`/`MonthlySalesSummary` diretamente | Criar `PlanogramSaleRepositoryContract` |
| 3 | `static::$landlord->enable()` nos models depende do raptor | Declarar `callcocam/laravel-raptor` como `require` no `composer.json` do pacote |
| 4 | `AutoPlanogramController` depende de `prism-php/prism` | Marcar como `suggest` no `composer.json` |
| 5 | `GondolaPdfPreviewController` depende de `barryvdh/laravel-dompdf` | Marcar como `suggest` no `composer.json` |
| 6 | Imports do frontend usam `@/` (alias do app) | Migrar para `#plannogram/` em batch; manter `@/` como re-export temporário |

---

*Última atualização: 17/03/2026*
