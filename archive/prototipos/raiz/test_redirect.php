<?php
require __DIR__ . '/vendor/autoload.php';

use Symfony\Component\HttpFoundation\RedirectResponse;

try {
    $resp = new RedirectResponse('/');
    $resp->setStatusCode(200);
    echo "Success!";
} catch (Exception $e) {
    echo "Exception: " . $e->getMessage();
}
