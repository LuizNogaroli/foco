<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Processo;
use App\Models\Requerimento;
use Illuminate\Support\Str;

class TestProcessosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $ufs = [
            'AC', 'AL', 'AP', 'AM', 'BA', 'CE', 'DF', 'ES', 'GO', 'MA', 
            'MT', 'MS', 'MG', 'PA', 'PB', 'PR', 'PE', 'PI', 'RJ', 'RN', 
            'RS', 'RO', 'RR', 'SC', 'SP', 'SE', 'TO'
        ];

        $tipos = [
            'Cessão de Uso Gratuito',
            'Cessão de Uso Oneroso',
            'Doação com Encargo',
            'Autorização de Uso'
        ];

        $prioridades = [
            'PAC', 'MCMV', 'Educação', 'Saúde', 'Segurança', 'Nenhuma'
        ];

        foreach ($ufs as $uf) {
            $createdForUf = 0;
            // Pega os números já existentes para esta UF no padrão UF2026XXX
            $existing = Requerimento::where('numero_requerimento', 'like', $uf . '2026%')
                ->pluck('numero_requerimento')
                ->map(function($req) {
                    return (int) substr($req, -3);
                })->toArray();

            for ($i = 1; $i <= 20; $i++) {
                if ($createdForUf >= 3) {
                    break;
                }
                
                if (in_array($i, $existing)) {
                    continue;
                }

                $numeroRequerimento = $uf . '2026' . str_pad($i, 3, '0', STR_PAD_LEFT);

                // Criar o requerimento base
                Requerimento::create([
                    'numero_requerimento' => $numeroRequerimento,
                    'tipo_requerimento' => $tipos[array_rand($tipos)],
                    'data_hora_recebimento' => now()->subDays(rand(1, 60))->format('Y-m-d H:i:s'),
                    'nup_sei' => '53000.' . rand(100000, 999999) . '/2026-' . rand(10, 99),
                    'cpf_cnpj_requerente' => rand(10, 99) . '.' . rand(100, 999) . '.' . rand(100, 999) . '/0001-' . rand(10, 99),
                    'nome_requerente' => 'Prefeitura Municipal Simulação ' . $uf . ' ' . $i,
                    'contato_requerente' => 'contato' . $i . '@' . strtolower($uf) . '.gov.br',
                    'cpf_cnpj_representante' => rand(100, 999) . '.' . rand(100, 999) . '.' . rand(100, 999) . '-' . rand(10, 99),
                    'nome_representante' => 'Representante Legal ' . $uf . ' ' . $i,
                    'contato_representante' => '(61) 9' . rand(8000, 9999) . '-' . rand(1000, 9999),
                    'projeto_prioritario' => (rand(1, 10) > 7) ? 'Sim' : 'Não',
                    'prioridade_legal' => $prioridades[array_rand($prioridades)],
                    'documentos_anexados' => [
                        ['nome' => 'Oficio_Solicitacao.pdf', 'url' => '#'],
                        ['nome' => 'Planta_Area.pdf', 'url' => '#']
                    ]
                ]);

                // Criar o processo associado
                Processo::create([
                    'numero_requerimento' => $numeroRequerimento,
                    'status_atual' => 'Aguardando Análise',
                    'tipo_requerimento' => $tipos[array_rand($tipos)],
                    'uf' => $uf,
                    'municipio' => 'Município Simulação ' . $i,
                    'tramitacao' => 'Novo',
                ]);

                $createdForUf++;
            }
        }
    }
}
