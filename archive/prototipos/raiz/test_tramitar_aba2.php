<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$request = Illuminate\Http\Request::create('/processos/33/tramitar', 'POST', [
    '_token' => 'dummy',
    'aba_atual' => '2',
    'next_aba' => 'index'
]);

$request->headers->set('HX-Request', 'true');

$response = $kernel->handle($request);
echo "Status Code: " . $response->getStatusCode() . "\n";
echo "Headers:\n";
foreach ($response->headers->all() as $key => $values) {
    echo "  $key: " . implode(', ', $values) . "\n";
}
echo "Content: " . substr($response->getContent(), 0, 100) . "\n";
