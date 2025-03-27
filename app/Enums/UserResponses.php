<?php

namespace App\Enums;

/**
 * Enum UserResponses
 *
 * Enumeração responsável por definir as respostas do usuário.
 *
 * @package App\Enums
 */
enum UserResponses: string {
    case CREATED = 'Usuario criado com sucesso';
    case ALREADY_EXIST = 'Usuario ja registrado';
}
