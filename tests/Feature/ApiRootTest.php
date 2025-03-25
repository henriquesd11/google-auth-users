<?php

namespace Tests\Feature;

use App\Enums\RootResponses;
use Tests\TestCase;

class ApiRootTest extends TestCase
{
    /**
     * Testa se a rota raiz da API retorna a resposta esperada.
     */
    public function test_api_root_returns_correct_response(): void
    {
        $response = $this->getJson('/api');

        $response->assertStatus(200)
            ->assertContent(json_encode(
                [
                    'message' => RootResponses::WELCOME
                ]
            ));
    }
}
