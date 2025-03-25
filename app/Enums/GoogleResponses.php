<?php

namespace App\Enums;

enum GoogleResponses: string {
    case ERROR_INTEGRATION = 'Erro na integracao com o Google';
    const SUCCESS = 'Sucesso';
}
