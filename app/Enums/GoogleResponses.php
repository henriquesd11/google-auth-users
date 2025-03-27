<?php

namespace App\Enums;

/**
 * Enum GoogleResponses
 *
 * Enumeração responsável por definir as respostas do Google.
 *
 * @package App\Enums
 */
enum GoogleResponses: string {
    case ERROR_INTEGRATION = 'Erro na integracao com o Google';
    const SUCCESS = 'Sucesso';
}
