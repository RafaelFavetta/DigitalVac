<?php
function validarCPF($cpf)
{
    // Remove caracteres não numéricos
    $cpf = preg_replace('/[^0-9]/', '', $cpf);

    // Verifica se tem 11 dígitos ou todos os dígitos iguais
    if (strlen($cpf) != 11 || preg_match('/^(\d)\1{10}$/', $cpf)) {
        error_log("CPF inválido: $cpf");
        return false;
    }

    // Calcula os dígitos verificadores
    for ($t = 9; $t < 11; $t++) {
        $soma = 0;
        for ($i = 0; $i < $t; $i++) {
            $soma += $cpf[$i] * (($t + 1) - $i);
        }
        $digito = ((10 * $soma) % 11) % 10;
        if ($cpf[$t] != $digito) {
            return false;
        }
    }
    return true;
}

function validarNIS($nis)
{
    // Remove caracteres não numéricos
    $nis = preg_replace('/[^0-9]/', '', $nis);

    // Verifica se tem 11 dígitos ou todos os dígitos iguais
    if (strlen($nis) != 11 || preg_match('/^(\d)\1{10}$/', $nis)) {
        return false;
    }

    // Calcula o dígito verificador
    $multiplicadores = [3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
    $soma = 0;

    for ($i = 0; $i < 10; $i++) {
        $soma += $nis[$i] * $multiplicadores[$i];
    }

    $resto = $soma % 11;
    $digito = ($resto < 2) ? 0 : (11 - $resto);

    return $nis[10] == $digito;
}

function validarTelefone($telefone)
{
    // Remove caracteres não numéricos
    $telefone = preg_replace('/[^0-9]/', '', $telefone);

    // Verifica se o telefone tem exatamente 10 ou 11 dígitos
    return strlen($telefone) === 10 || strlen($telefone) === 11;
}

function validarCEP($cep)
{
    // Remove caracteres não numéricos
    $cep = preg_replace('/[^0-9]/', '', $cep);

    // Verifica se o CEP tem exatamente 8 dígitos
    return strlen($cep) === 8;
}

function validarSUS($sus)
{
    // Remove caracteres não numéricos
    $sus = preg_replace('/[^0-9]/', '', $sus);

    // Verifica se tem 15 dígitos
    if (strlen($sus) != 15) {
        return false;
    }

    // Calcula o dígito verificador
    $multiplicadores = [15, 14, 13, 12, 11, 10, 9, 8, 7, 6, 5, 4, 3, 2];
    $soma = 0;

    for ($i = 0; $i < 14; $i++) {
        $soma += $sus[$i] * $multiplicadores[$i];
    }

    $resto = $soma % 11;
    $digito = ($resto == 0 || $resto == 1) ? 0 : (11 - $resto);

    return $sus[14] == $digito;
}
?>