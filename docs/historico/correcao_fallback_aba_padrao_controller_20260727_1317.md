# Correção do Fallback de Aba Padrão no ProcessoController

**Arquivo Modificado:** `app/Http/Controllers/ProcessoController.php`

## Motivo da Alteração
O usuário relatou repetidamente que, ao interagir com a Aba 7 (Validação - Chefia) e clicar em "Salvar e Enviar", o sistema continuava redirecionando/voltando para a Aba 1.

Após investigação profunda da arquitetura do controller, foi identificado a causa raiz histórica do problema:
No método `show(Processo $processo, Request $request)` do `ProcessoController.php`, a variável `$aba` era obtida com um valor padrão fixo caso a query string `?aba=` não estivesse presente na URL:
```php
$aba = $request->query('aba', 1);
```
Isso fazia com que **qualquer** navegação sem o parâmetro `?aba=N` explícito na URL (como redirecionamentos via `back()`, reloads de página, cliques diretos em links de processo ou retornos do HTMX) desabasse involuntariamente na **Aba 1**. 

Mesmo que o processo estivesse no status `"Validação - Chefia"` ou `"Validação - Coordenação"` (que pertencem à Aba 7), o controller forçava a renderização da Aba 1 por conta do fallback `1`.

**Solução:**
Alterou-se o método `show()` para que, na ausência do parâmetro `?aba=` na URL, o controller consulte dinamicamente o método `$this->getAbaEStatus($processo->status_atual, $perfil)` e identifique em qual aba aquele status deve residir. 
Assim, processos no status de Validação/Deliberação automaticamente abrem na Aba 7 sem retroceder para a Aba 1.

## 1. Estado Anterior (Antes)
```php
    public function show(Processo $processo, Request $request)
    {
        $aba = $request->query('aba', 1);
```

## 2. Estado Novo (Depois)
```php
    public function show(Processo $processo, Request $request)
    {
        if ($request->has('aba')) {
            $aba = (int) $request->query('aba');
        } else {
            $perfil = $this->getPerfilAtual();
            $resultado = $this->getAbaEStatus($processo->status_atual, $perfil);
            $aba = $resultado['aba'] ?? 1;
        }
```

## 3. Plano de Rollback / Desfazer
1. Abra `app/Http/Controllers/ProcessoController.php`.
2. Localize a função `show()`.
3. Substitua a estrutura `if ($request->has('aba')) { ... }` pela linha original `$aba = $request->query('aba', 1);`.
