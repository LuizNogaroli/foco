<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Processo;
use Tests\TestCase;

class ReproTramitarTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config()->set('database.connections.sqlite.database', database_path('database.sqlite'));
        config()->set('database.default', 'sqlite');
        \DB::purge('sqlite');
        \DB::reconnect('sqlite');
    }

    public function test_repro_167(): void
    {
        $user = User::where('email', 'admin@spu.gov.br')->first() ?? User::first();
        $this->actingAs($user);

        $p = Processo::find(167);
        $this->assertNotNull($p, 'processo 167 nao existe');

        $response = $this->post(route('processos.tramitar', $p->id), [
            'aba_atual' => '1',
            'next_aba' => 'index',
            'conceituacao_imovel' => 'Terreno/acrescido de marinha',
            'numero_requerimento' => $p->numero_requerimento,
            'tipo_requerimento' => $p->tipo_requerimento,
            'data_requerimento' => '01/01/2026 10:00',
            'processo_sei' => '12345.678901/2026-11',
            'solicitacao_criacao_rip' => '',
            'rips' => [
                json_encode(['numero_rip' => '5047315', 'destinacao_terreno' => 'Integral', 'area_terreno_parcial' => '', 'destinacao_imovel' => 'Integral', 'area_imovel_parcial' => '']),
            ],
            'cadastros_minimos' => [
                json_encode(['cep' => '70000000', 'logradouro' => 'Rua Teste', 'numero' => '100', 'complemento' => '', 'municipio' => 'Brasília', 'uf' => 'DF', 'area' => '500', 'observacoes' => '', 'latitude' => '', 'longitude' => '', 'modo_localizacao' => 'CEP', 'destinacao_terreno' => 'Integral', 'area_terreno_parcial' => '', 'destinacao_imovel' => 'Integral', 'area_imovel_parcial' => '']),
            ],
        ]);

        $response->assertStatus(302);

        $foco = $p->foco;
        $this->assertNotNull($foco, 'foco deveria ter sido criado');
        $rip = $foco->rips()->where('numero_rip', '5047315')->first();
        $this->assertNotNull($rip, 'rip deveria ter sido salvo');
        $this->assertNull($rip->area_terreno_parcial, 'area_terreno_parcial vazia deve virar NULL');
        $this->assertNull($rip->area_imovel_parcial, 'area_imovel_parcial vazia deve virar NULL');
        $cad = $foco->cadastrosMinimos()->first();
        $this->assertNotNull($cad, 'cadastro minimo deveria ter sido salvo');
        $this->assertEquals(500, (float) $cad->area, 'area deve ser persistida como numero');
    }
}
