<?php
\App\Models\Tramite::whereNull('acao')->get()->each(function($t) {
    $d = is_string($t->dados_snapshot) ? json_decode($t->dados_snapshot, true) : $t->dados_snapshot;
    if(isset($d['aba_atual'])) {
        $t->update([
            'acao' => 'Aba ' . $d['aba_atual'] . ' Salva',
            'etapa' => 'Preenchimento - Aba ' . $d['aba_atual']
        ]);
    }
});
echo "Fix completo\n";
