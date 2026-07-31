<?php
require __DIR__ . '/vendor/autoload.php';

use Symfony\Component\HttpFoundation\RedirectResponse;

$resp = new RedirectResponse('/');
$resp->setContent('');
echo "Content length: " . strlen($resp->getContent()) . "\n";
echo "Content: " . $resp->getContent() . "\n";
