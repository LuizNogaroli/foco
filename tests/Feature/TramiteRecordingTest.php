<?php

namespace Tests\Feature;

use App\Models\Processo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TramiteRecordingTest extends TestCase
{
    use RefreshDatabase;

    private function criarProcesso(): Processo
    {
        return Processo::create([
            'numero_requerimento' => 'PR' . now()->format('YmdHis'),
            'status_atual' => 'Indicação do Imóvel',
            'tipo_requerimento' => 'Ocupação',
            'tramitacao' => 'Ativo',
        ]);
    }

    public function test_aba1_salva_registra_acao_etapa_e_usuario(): void
    {
        $user = User::factory()->create();
        $processo = $this->criarProcesso();

        $this->actingAs($user)->post(route('processos.tramitar', $processo->id), [
            'aba_atual' => '1',
            'next_aba' => 'index',
            'conceituacao_imovel' => 'Imóvel de teste',
        ])->assertSessionHasNoErrors();

        $tramite = $processo->tramites()->first();

        $this->assertNotNull($tramite, 'Nenhum trâmite foi criado.');
        $this->assertSame('Aba 1 Salva', $tramite->acao);
        $this->assertSame('Preenchimento - Aba 1', $tramite->etapa);
        $this->assertSame($user->id, $tramite->usuario_id);
        $this->assertArrayHasKey('conceituacao_imovel', $tramite->dados_snapshot);
    }

    public function test_segundo_salvamento_registra_aba_alterada(): void
    {
        $user = User::factory()->create();
        $processo = $this->criarProcesso();

        $this->actingAs($user)->post(route('processos.tramitar', $processo->id), [
            'aba_atual' => '1', 'next_aba' => 'index', 'conceituacao_imovel' => 'Primeira versão',
        ]);
        $this->actingAs($user)->post(route('processos.tramitar', $processo->id), [
            'aba_atual' => '1', 'next_aba' => 'index', 'conceituacao_imovel' => 'Segunda versão',
        ]);

        $this->assertSame('Aba 1 Salva', $processo->tramites()->orderBy('id')->first()->acao);
        $this->assertSame('Aba 1 Alterada', $processo->tramites()->orderByDesc('id')->first()->acao);
    }

    public function test_resposta_devolucao_registra_devolucao_resolvida_antes_do_salvamento(): void
    {
        $user = User::factory()->create();
        $processo = $this->criarProcesso();

        $this->actingAs($user)->post(route('processos.tramitar', $processo->id), [
            'aba_atual' => '1', 'next_aba' => 'index',
            'conceituacao_imovel' => 'Imóvel',
            'resposta_devolucao' => 'Pendências corrigidas.',
        ]);

        $tramites = $processo->tramites()->orderBy('id')->get();
        $this->assertCount(2, $tramites);
        $this->assertSame('Devolução Resolvida', $tramites[0]->acao);
        $this->assertSame('Pendências corrigidas.', $tramites[0]->justificativa);
        $this->assertSame('Aba 1 Salva', $tramites[1]->acao);
    }

    public function test_assinatura_aba7_registra_manifestacao_com_perfil(): void
    {
        $user = User::factory()->create();
        $processo = $this->criarProcesso();

        $this->actingAs($user)->post(route('processos.tramitar', $processo->id), [
            'aba_atual' => '7',
            'next_aba' => 'index',
            'acao_aba7' => 'chefia',
            'decl_chefia_opcao' => 'suficiente',
        ])->assertSessionHasNoErrors();

        $tramite = $processo->tramites()->orderByDesc('id')->first();
        $this->assertSame('Manifestação', $tramite->acao);
        $this->assertSame('Chefia', $tramite->etapa);
        $this->assertSame($user->id, $tramite->usuario_id);
        $this->assertArrayHasKey('assinatura_chefia_nome', $tramite->dados_snapshot);
        $this->assertSame('Validação - Coordenação', $processo->fresh()->status_atual);
    }

    public function test_devolucao_na_aba7_registra_manifestacao_e_devolvido(): void
    {
        $user = User::factory()->create();
        $processo = $this->criarProcesso();

        $this->actingAs($user)->post(route('processos.tramitar', $processo->id), [
            'aba_atual' => '7',
            'next_aba' => 'index',
            'acao_aba7' => 'chefia',
            'decl_chefia_opcao' => 'insuficiente',
            'obs_chefia' => 'Informações insuficientes.',
        ])->assertSessionHasNoErrors();

        $tramites = $processo->tramites()->orderBy('id')->get();
        $this->assertCount(2, $tramites);
        $this->assertSame('Manifestação', $tramites[0]->acao);
        $this->assertSame('Devolvido', $tramites[1]->acao);
        $this->assertSame('Informações insuficientes.', $tramites[1]->justificativa);
        $this->assertSame('Devolvido', $processo->fresh()->tramitacao);
    }

    public function test_devolver_registra_acao_devolvido_com_justificativa(): void
    {
        $user = User::factory()->create();
        $processo = $this->criarProcesso();

        $this->actingAs($user)->post(route('processos.devolver', $processo->id), [
            'aba' => '1',
            'motivo_devolucao' => 'Faltam documentos.',
        ])->assertSessionHasNoErrors();

        $tramite = $processo->tramites()->orderByDesc('id')->first();
        $this->assertSame('Devolvido', $tramite->acao);
        $this->assertSame('Indicação do Imóvel', $tramite->etapa);
        $this->assertSame('Faltam documentos.', $tramite->justificativa);
        $this->assertSame($user->id, $tramite->usuario_id);
        $this->assertSame('Devolvido', $processo->fresh()->tramitacao);
        $this->assertSame('Indicação do Imóvel', $processo->fresh()->status_atual);
    }

    public function test_receber_devolucao_registra_acao_recebido_e_reativa_processo(): void
    {
        $user = User::factory()->create();
        $processo = $this->criarProcesso();

        $this->actingAs($user)->post(route('processos.devolver', $processo->id), [
            'aba' => '1',
            'motivo_devolucao' => 'Faltam documentos.',
        ]);

        $this->actingAs($user)->post(route('processos.receber-devolucao', $processo->id), [
            'aba' => '1',
        ])->assertJson(['success' => true]);

        $processo->refresh();
        $this->assertSame('Normal', $processo->tramitacao);

        $tramite = $processo->tramites()->orderByDesc('id')->first();
        $this->assertSame('Recebido', $tramite->acao);
        $this->assertSame($user->id, $tramite->usuario_id);
        $this->assertSame('Faltam documentos.', $tramite->justificativa);
    }

    public function test_aba2_mostra_box_devolucao_resolvida(): void
    {
        $user = User::factory()->create();
        $processo = $this->criarProcesso();

        $this->actingAs($user)->post(route('processos.devolver', $processo->id), [
            'aba' => '1',
            'motivo_devolucao' => 'Faltam documentos.',
        ]);

        $this->actingAs($user)->post(route('processos.tramitar', $processo->id), [
            'aba_atual' => '1',
            'next_aba' => 'index',
            'conceituacao_imovel' => 'Imóvel ajustado',
            'resposta_devolucao' => 'Pendências corrigidas.',
        ]);

        $this->actingAs($user)->get(route('processos.show', ['processo' => $processo->id, 'aba' => 2]))
            ->assertOk()
            ->assertSee('Devolução Resolvida')
            ->assertSee('Pendências corrigidas.');
    }
}
