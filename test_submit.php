<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$user = \App\Models\User::first();
auth()->login($user);

$request = Illuminate\Http\Request::create('/processos/33/tramitar', 'POST', [
    '_token' => csrf_token(),
    'aba_atual' => '1',
    'next_aba' => 'index'
]);

$response = $kernel->handle($request);

echo "Status Code: " . $response->getStatusCode() . "\n";
echo "Location: " . $response->headers->get('Location') . "\n";
echo "HX-Redirect: " . $response->headers->get('HX-Redirect') . "\n";
