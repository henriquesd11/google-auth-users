<?php

namespace App\Enums;

enum UserResponses: string {
    case CREATED = 'Usuario criado com sucesso';

    case ALREADY_EXIST = 'Usuario ja registrado';
}
