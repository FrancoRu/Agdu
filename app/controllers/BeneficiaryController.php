<?php
require_once __DIR__ . '/../services/BeneficiaryService.php';
require_once __DIR__ . '/../libs/chargeFile.php';
require_once __DIR__ . '/../services/FTPManagement.php';
require_once __DIR__ . '/../libs/validation.php';
class BeneficiaryController
{

  public static function chargeUser()
  {
    unset($_SESSION['error']);
    if (!validateCUIL()) {
      loadView(__DIR__ . '/../views/Beneficiary/BeneficiaryIngress.php', 400);
      exit();
    }
    $_SESSION['user_id'] = $_GET['user_id'];
    loadView(__DIR__ . '/../views/Beneficiary/BeneficiaryHome.php', 200);
    exit();
  }

  public static function setFile()
  {
    if (!validateForm($_POST)) {
      loadView(__DIR__ . '/../views/Beneficiary/BeneficiaryIngress.php', 403);
      exit();
    }
    $args = self::findArgs();
    if (BeneficiaryService::setInfo($args)) {
      session_destroy();
      echo json_encode(['status' => 200]);
      exit();
    } else {
      $_SESSION['error'] = 'Hubo problemas al cargar los datos';
      loadView(__DIR__ . '/../views/Beneficiary/BeneficiaryIngress.php', 403);
      exit();
    }
  }

  public static function validate()
  {
    foreach ($_POST as $key => $value) {
      if (!is_string($key) || !is_string($value)) {
        return false;
      }
      if (!self::isValidString($value)) {
        return false;
      }
      if (strpos($key, 'name') !== false) {
        if (!self::validateIsString($value)) {
          return false;
        }
      }
    }
    return true;
  }


  private static function validateIsString($cadena)
  {
    $regex = "/^[a-zA-Z]*$/";
    return preg_match($regex, $cadena);
  }

  // Función para verificar que una cadena es segura
  private static function isValidString($cadena)
  {
    $escapedString = htmlspecialchars($cadena, ENT_QUOTES, 'UTF-8');
    return $escapedString === $cadena;
  }

  private static function findArgs()
  {
    $args = [];
    $counterChildren = 1;
    foreach ($_POST as $key => $value) {

      if ($key === 'action') {
        continue;
      }

      $band = strpos($key, 'name') === 0 || strpos($key, 'education') === 0;

      if ($band && strlen($value) === 0) {
        continue;
      }

      if ($band) {
        if (!isset($args['childrens'])) {
          $args['childrens'] = [
            'value' => [],
            'amount' => ''
          ];
        }
        if (!isset($args['childrens']['value']['children-' . $counterChildren])) {
          $test = [];
          $args['childrens']['value']['children-' . $counterChildren] = [];
          $test =
            [
              'name' => $_FILES['formFile-' . explode('-', $key)[1]]['name'],
              'file_children' =>  $_FILES['formFile-' . explode('-', $key)[1]]['tmp_name'],
              'user_id' => $_SESSION['user_id']
            ];
          if ($_ENV['DB_DATABASE'] === 'agdu') {
            $url = self::moveFilesLocal([...$test]);
          } else {
            $ftpConnection = FTPManagement::getInstance();
            $url = $ftpConnection->uploadFile([...$test]);
          }
          if ($url !== null) {
            $args['childrens']['value']['children-' . $counterChildren]['url_file'] = $url;
          }
          $args['childrens']['value']['children-' . $counterChildren]['file_name'] = $_FILES['formFile-' . explode('-', $key)[1]]['name'];
          // $args['childrens']['value']['children-' . $counterChildren]['file_children'] = file_get_contents($_FILES['formFile-' . explode('-', $key)[1]]['tmp_name']);
        }
        $args['childrens']['value']['children-' . $counterChildren][$key] = $value;

        if (strpos($key, 'education') !== false) {
          $counterChildren++;
        }
      } else {
        $args[$key] = $value;
      }
    }
    $args['childrens']['amount'] = $counterChildren - 1;
    return $args;
  }

  private static function validateExtension($arg)
  {
    $extension = strtolower(pathinfo($arg['name'], PATHINFO_EXTENSION));
    $extensionAccepted = ['pdf', 'png', 'jpg', 'jpeg'];
    return in_array($extension, $extensionAccepted);
  }

  private static function moveFilesLocal($arg)
  {
    $result = null;
    if (self::validateExtension($arg)) {
      $downloadFolder = __DIR__ . '/../../files/constancias';
      $userFolder = $downloadFolder . '/' . $arg['user_id'];
      if (!file_exists($userFolder)) {
        mkdir($userFolder, 0777, true); // 0777 es un ejemplo de permisos, ajusta según tus necesidades de seguridad
      }
      // Mueve el archivo a la carpeta del usuario
      $destination = $userFolder . '/' . $arg['name'];
      $mov = move_uploaded_file($arg['file_children'], $destination);
      if ($mov) {
        $result = $destination . $arg['file_children'];
        return $result;
      }
    }
    return $result;
  }
}
