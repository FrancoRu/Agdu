<?php

use Respect\Validation\Validator as v;

function validateCUIL()
{
  $cuil = $_GET['user_id'] ?? '';
  // Validación del campo CUIL
  $cuilValidator = v::notEmpty()
    ->number()
    ->length(11, 11)
    ->positive()
    ->noWhitespace();

  try {
    $cuilValidator->check($cuil);
    return true;
  } catch (\Respect\Validation\Exceptions\ValidationException $e) {
    $_SESSION['error'] = 'Ingrese un CUIL válido (11 dígitos sin guiones)';
    return false;
    exit();
  }
}

function validateFormDataUser($formData)
{

  $lastname = $formData['lastname'] ?? '';
  $username = $formData['username'] ?? '';
  $banco = $formData['banco'] ?? '';
  $tipoCuenta = $formData['tipoCuenta'] ?? '';
  $email = $formData['email'] ?? '';

  // Validar Apellido
  if (!validateName($lastname, 'apellido')) {
    return false;
  }

  // Validar Nombre
  if (!validateName($username, 'nombre')) {
    return false;
  }

  // Validar CBU CVU o Alias
  // if (!preg_match("/^[0-9.]+$/", $banco) && !preg_match("/^[a-zA-Z]+(?:\.[a-zA-Z]+)*$/", $banco)) {
  //   $_SESSION['error'] = 'Ingrese un C.B.U / C.V.U / Alias válido.';
  //   return false;
  // }
  if (!preg_match("/^[0-9]*|[\p{L}]+(?:[\p{L}0-9]*\.[\p{L}0-9]*)*$/", $banco)) {
    $_SESSION['error'] = 'Ingrese un C.B.U / C.V.U / Alias válido.';
    return false;
  }

  // Validar Tipo de Cuenta
  if (!validateArray($tipoCuenta, ["0", "1"], 'tipo de cuenta')) {
    return false;
  }

  // Validar Email
  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['error'] = 'Ingrese un correo electrónico válido.';
    return false;
  }
  return true;
}

function validateName($value, $msg)
{
  $validatename = v::notEmpty()->stringType()->regex('/^[\p{L}]+(?: [\p{L}]+)*$/u');
  try {
    $validatename->check($value);
    return true;
  } catch (\Respect\Validation\Exceptions\ValidationException $e) {
    $_SESSION['error'] = "Ingrese un $msg válido (solo letras y espacios entre palabras)";
    return false;
  }
}
function validateArray($value, $array, $msj)
{
  if (!in_array($value, $array)) {
    $_SESSION['error'] = "Seleccione un $msj válido.";
    return false;
  }
  return true;
}

function validateForm($args)
{
  $newArgs = [];
  $form = [];

  foreach ($args as $key => $value) {
    if (!isValidString($value)) {
      $_SESSION['error'] = 'Se detectaron parámetros peligrosos';
      return false;
    }

    if (strpos($key, 'name-') !== false) {
      if (!empty($value)) {
        if (!validateName($value, 'nombre y apellido de hijo')) {
          return false;
        }
        $newArgs[$key] = $value;
      }
    } elseif (strpos($key, 'education-') !== false) {
      if (!validateArray($value, ["0", "1", "2"], 'nivel de educacion')) {
        return false;
      }
      $newArgs[$key] = $value;
    } else {
      $form[$key] = $value;
      $newArgs[$key] = $value;
    }
  }

  return validateFormDataUser($form);
}


function isValidString($value)
{
  $escapedString = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
  return $escapedString === $value;
}
