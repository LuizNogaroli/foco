# Histórico de Alterações — Correção de Chaves Primárias do Foco para PostgreSQL

## 1. Estado Anterior (Antes)

Nos modelos `FocoAba1.php`, `FocoAba2.php` e `FocoAba3.php`:
```php
class FocoAba1 extends Model {
    protected $table = 'foco_aba1';
}
```
*(Não havia especificação de chave primária e incremento. O Laravel assumia `id` e `$incrementing = true`).*

---

## 2. Estado Novo (Depois)

Nos modelos `FocoAba1.php`, `FocoAba2.php` e `FocoAba3.php`:
```php
class FocoAba1 extends Model {
    protected $table = 'foco_aba1';
    protected $primaryKey = 'foco_id';
    public $incrementing = false;
}
```

---

## 3. Plano de Rollback / Desfazer

Caso queira reverter a definição de chaves primárias nos modelos:

1. Execute no terminal:
   ```bash
   git revert 82b8f78a6ff607e4c5b3670c5ff84f18db262f22
   ```
   *(Substitua pelo hash do commit correspondente se necessário).*
2. Faça o push para o repositório remoto:
   ```bash
   git push origin main
   ```
