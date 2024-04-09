<?php

require_once __DIR__ . '/../services/AdministratorService.php';
require_once __DIR__ . '/../libs/chargeFile.php';
require_once __DIR__ . '/../services/FTPManagement.php';
require_once __DIR__ . '/../libs/token.php';

class AdministratorController
{

  private static function authAdministrator()
  {
    if (!isset($_SESSION['token']) || !validateToken($_SESSION['token'])) {
      $_SESSION['error'] = 'Usuario no autorizado';
      return false;
    }
    unset($_SESSION['error']);
    return true;
  }

  public static function PanelOrLogin()
  {
    if (!self::authAdministrator()) {
      loadView(__DIR__ . '/../views/Administrator/AdministratorLogin.php', 200);
    }
    loadView(__DIR__ . '/../views/Administrator/AdministratorPanel.php', 200);
  }

  public static function readBeneficiary()
  {
    return AdministratorService::readData();
  }

  public static function downloadFile()
  {
    if (!self::authAdministrator()) {
      loadView(__DIR__ . '/../views/Administrator/AdministratorLogin.php', 403);
    }
    if (!isset($_POST['filename']) || !isset($_POST['filepath'])) {
      echo "Nombre de file no proporcionado.";
      exit();
    }
    $args = [
      'filename' => $_POST['filename'],
      'filepath' => $_POST['filepath']
    ];

    $ftpManager = FTPManagement::getInstance();
    try {
      $ftpManager->downloadFile($args);
    } catch (Exception $e) {
      echo "Error: " . $e->getMessage();
    }
  }

  public static function login()
  {
    if (!isset($_POST['username'])) {
      $_SESSION['error'] = 'Por favor ingresar un nombre de usuario';
      loadView(__DIR__ . '/../views/Administrator/AdministratorLogin.php', 404);
    }
    error_log('username ok');
    if (!isset($_POST['password'])) {
      $_SESSION['error'] = 'Por favor ingresar una contraseña';
      loadView(__DIR__ . '/../views/Administrator/AdministratorLogin.php', 404);
    }
    error_log('password ok');
    $args = [
      'username' => $_POST['username'],
      'password' => $_POST['password']
    ];

    if (!self::verifyCredential($args['username'], $args['password'])) {
      $_SESSION['error'] = 'Usuario o contraseña invalidos';
      loadView(__DIR__ . '/../views/Administrator/AdministratorLogin.php', 403);
    }

    $token = generateToken($args['username']);
    error_log(json_encode($token));
    unset($_SESSION['error']);
    $_SESSION['token'] = $token;
    loadView(__DIR__ . '/../views/Administrator/AdministratorPanel.php', 200);
  }

  public static function logOut()
  {
    session_unset();
    session_destroy();
    unset($_SESSION['error']);
    loadView(__DIR__ . '/../views/Administrator/AdministratorLogin.php', 200);
  }
  private static function verifyCredential($username, $password)
  {
    return password_verify($username, $_ENV['USERNAME_ROOT_AGDU']) && password_verify($password, $_ENV['PASSWORD_ROOT_AGDU']);
  }

  public static function changeValue($username, $password, $newValue, bool $cond)
  {

    //cond true cambia el username
    //cond false cambia el password

    if (!password_verify($username, $_ENV['USERNAME_ROOT_AGDU']) || !password_verify($password, $_ENV['PASSWORD_ROOT_AGDU'])) {
      $_SESSION['error'] = 'Error con las credenciales del usuario';
      loadView(__DIR__ . '/../views/Administrator/AdministratorPanel.php', 403);
    }
    $hashNewValue = password_hash($newValue, PASSWORD_BCRYPT);
    error_log('nuevo hash: ' . $hashNewValue);
    if ($cond) {
      self::updateVariableEnv('USERNAME_ROOT_AGDU', $hashNewValue);
    } else {
      self::updateVariableEnv('PASSWORD_ROOT_AGDU', $hashNewValue);
    }
    unset($_SESSION['error']);
    loadView(__DIR__ . '/../views/Administrator/AdministratorPanel.php', 200);
  }

  private static function updateVariableEnv($variable, $value)
  {
    // Actualiza la variable de entorno en el script actual
    putenv("$variable=$value");
    $file = __DIR__ . '/../.env';
    // Lee el contenido del archivo .env
    $contenido = file_get_contents($file);

    // Busca la variable de entorno y actualiza su valor
    $newContent = preg_replace("/{$variable}=.*/", "{$variable}={$value}", $contenido);

    // Escribe el nuevo contenido en el archivo .env
    $success = file_put_contents($file, $newContent);

    if ($success !== false) {
      // Éxito
      error_log("La variable de entorno $variable se actualizó correctamente en el archivo .env.");
    } else {
      // Error
      error_log("Error al intentar actualizar la variable de entorno $variable en el archivo .env.");
    }
  }

  public static function modify()
  {

    if (!isset($_POST['username']) || !isset($_POST['password']) || !isset($_POST['newValue'])) {
      error_log('error' . json_encode($_POST));
      loadView(__DIR__ . '/../views/Administrator/AdministratorChange.php', 200);
    } else {
      error_log('exito ' . json_encode($_POST));
      self::changeValue($_POST['username'], $_POST['password'], $_POST['newValue'], isset($_POST['change']) ? true : false);
    }
    loadView(__DIR__ . '/../views/Administrator/AdministratorChange.php', 200);
  }
  public static function downloadXLSX()
  {
    AdministratorService::getXLXS();
  }
}
