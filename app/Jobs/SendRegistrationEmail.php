<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

/**
 * Class SendRegistrationEmail
 *
 * Job responsável por enviar um email de confirmação de cadastro.
 *
 * @package App\Jobs
 */
class SendRegistrationEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Email do usuário.
     *
     * @var string
     */
    protected string $email;

    /**
     * Construtor do SendRegistrationEmail.
     *
     * @param string $email
     */
    public function __construct(string $email)
    {
        $this->email = $email;
    }

    /**
     * Executa o job.
     *
     * @return void
     */
    public function handle(): void
    {
        Mail::raw('Seu cadastro foi concluído com sucesso!', function ($message) {
            $message->to($this->email)
                ->subject('Cadastro Concluído');
        });
    }
}
