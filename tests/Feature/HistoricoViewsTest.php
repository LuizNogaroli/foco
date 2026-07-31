<?php

namespace Tests\Feature;

use App\Models\Processo;
use App\Models\Tramite;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HistoricoViewsTest extends TestCase
{
    use RefreshDatabase;

    private function criarProcessoComHistoricoReal(): array
    {
        $user = User::factory()->create();
        $processo = Processo::create([
            'numero_requerimento' => 'SP2026003',
            'status_atual' => 'Análise de Viabilidade',
            'tipo_requerimento' => 'Cessão de Uso Oneroso',
            'uf' => 'SP',
            'municipio' => 'Município Simulação 3',
            'tramitacao' => 'Normal',
        ]);

        $snapshotBase = [
            'aba_atual' => '1', 'next_aba' => 'index',
            'numero_requerimento' => 'SP2026003',
            'tipo_requerimento' => 'Cessão de Uso Oneroso',
            'cpf_cnpj_requerente' => '12345678901',
            'nome_requerente' => 'Empresa Teste',
            'conceituacao_imovel' => 'Imóvel de teste',
            'rips' => ['2026001'],
        ];

        $sequencia = [
            ['acao' => 'Aba 1 Salva', 'etapa' => 'Preenchimento - Aba 1', 'justificativa' => null, 'snapshot' => $snapshotBase],
            ['acao' => 'Aba 2 Salva', 'etapa' => 'Preenchimento - Aba 2', 'justificativa' => null, 'snapshot' => array_merge($snapshotBase, ['situacao_ocupacional' => 'desocupado', 'tempo_desocupacao' => '2 anos'])],
            ['acao' => 'Devolvido', 'etapa' => 'Análise de Viabilidade', 'justificativa' => 'asdasdasdasd', 'snapshot' => $snapshotBase],
            ['acao' => 'Recebido', 'etapa' => 'Análise de Viabilidade', 'justificativa' => 'asdasdasdasd', 'snapshot' => $snapshotBase],
            ['acao' => 'Devolução Resolvida', 'etapa' => 'Preenchimento - Aba 1', 'justificativa' => 'inseri novo rip no requerimento rip 2026001', 'snapshot' => array_merge($snapshotBase, ['resposta_devolucao' => 'inseri novo rip no requerimento rip 2026001'])],
            ['acao' => 'Aba 1 Alterada', 'etapa' => 'Preenchimento - Aba 1', 'justificativa' => null, 'snapshot' => $snapshotBase],
            ['acao' => 'Aba 2 Alterada', 'etapa' => 'Preenchimento - Aba 2', 'justificativa' => null, 'snapshot' => array_merge($snapshotBase, ['situacao_ocupacional' => 'ocupado'])],
        ];

        foreach ($sequencia as $i => $item) {
            Tramite::create([
                'processo_id' => $processo->id,
                'acao' => $item['acao'],
                'etapa' => $item['etapa'],
                'usuario_id' => $user->id,
                'justificativa' => $item['justificativa'],
                'dados_snapshot' => $item['snapshot'],
                'created_at' => now()->subMinutes(30 - $i),
            ]);
        }

        return [$user, $processo];
    }

    public function test_todas_as_telas_de_historico_renderizam(): void
    {
        [$user, $processo] = $this->criarProcessoComHistoricoReal();

        $rotas = [
            'processos.historico',
            'processos.historico.escolha',
            'processos.historico.modelo-b',
            'processos.historico.modelo-c',
            'processos.historico.modelo-d',
            'processos.historico.modelo-e',
            'processos.historico.modelo-f',
            'processos.historico.modelo-g',
        ];

        foreach ($rotas as $rota) {
            $this->actingAs($user)->get(route($rota, $processo->id))
                ->assertOk()
                ->assertSee('Histórico');
        }
    }

    public function test_modelo_b_mostra_devolucao_e_resolucao(): void
    {
        [$user, $processo] = $this->criarProcessoComHistoricoReal();

        $this->actingAs($user)->get(route('processos.historico.modelo-b', $processo->id))
            ->assertOk()
            ->assertSee('Devolvido')
            ->assertSee('Recebido')
            ->assertSee('Devolução Resolvida')
            ->assertSee('asdasdasdasd')
            ->assertSee('inseri novo rip no requerimento rip 2026001');
    }

    public function test_modelo_a_mostra_os_registros(): void
    {
        [$user, $processo] = $this->criarProcessoComHistoricoReal();

        $this->actingAs($user)->get(route('processos.historico', $processo->id))
            ->assertOk()
            ->assertSee('Histórico Cronológico do Processo')
            ->assertDontSee('Nenhum evento registrado no histórico ainda.')
            ->assertSee('Devolução - Origem: Análise de Viabilidade')
            ->assertSee('Devolução Resolvida')
            ->assertSee('asdasdasdasd')
            ->assertSee('inseri novo rip no requerimento rip 2026001');
    }
}
