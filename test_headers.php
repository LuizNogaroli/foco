<?php
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$request = Illuminate\Http\Request::create('/processos/1/tramitar', 'POST');
$request->headers->set('HX-Request', 'true');
// Just checking if RedirectResponse with 200 works in Laravel
$response = new Illuminate\Http\RedirectResponse('http://127.0.0.1:8000/');
$response->setStatusCode(200);
$response->header('HX-Redirect', $response->getTargetUrl());
$response->setContent('');

echo "Status: " . $response->getStatusCode() . "\n";
echo "Headers:\n" . $response->headers . "\n";
echo "Content: " . $response->getContent() . "\n";
