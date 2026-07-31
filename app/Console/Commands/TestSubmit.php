<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Http\Request;

class TestSubmit extends Command
{
    protected $signature = 'test:submit';
    protected $description = 'Test the submit redirect';

    public function handle()
    {
        $user = \App\Models\User::first();
        auth()->login($user);

        $request = Request::create('/processos/33/tramitar', 'POST', [
            '_token' => csrf_token(),
            'aba_atual' => '1',
            'next_aba' => 'index'
        ]);
        $request->headers->set('HX-Request', 'true');

        $kernel = app()->make(\Illuminate\Contracts\Http\Kernel::class);
        $response = $kernel->handle($request);

        $this->info("Status Code: " . $response->getStatusCode());
        $this->info("Location: " . $response->headers->get('Location'));
        $this->info("HX-Redirect: " . $response->headers->get('HX-Redirect'));
    }
}
