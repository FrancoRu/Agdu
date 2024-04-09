<?php

require_once 'DB/DBManagment.php';

use Ramsey\Uuid\Uuid;

class BeneficiaryService
{
  private static $instance = null;

  private static function dbInitialize()
  {
    if (self::$instance === null) {
      self::$instance = DBManagment::getInstance();
    }
    return self::$instance;
  }

  public static function searchUser($args)
  {
    self::dbInitialize();

    if (!is_array($args)) {
      $args = array($args);
    }

    $query = "SELECT B.user_id FROM Beneficiary B WHERE B.user_id = ?";

    $result = self::$instance->query($query, $args);

    return $result->get_result()->fetch_assoc();
  }

  private static function searchChildren($args)
  {

    $query = "SELECT C.children_id FROM Children C WHERE C.user_id = ? AND C.name_children = ?";

    $result = self::$instance->query($query, $args);

    // Obtener la fila como un arreglo asociativo
    $row = $result->get_result()->fetch_assoc();
    if ($row !== null) {
      $childrenId = $row['children_id'];

      return $childrenId;
    }
    return null;
    // Extraer el valor de 'children_id'

  }

  public static function setInfo($args)
  {
    $argsUser = self::getArgsUser([...$args]);
    if (self::setUser($argsUser) === false) {
      $_SESSION['message'] = 'Error al cargar o modificar el CUIL' . $_SESSION['user_id'];
      return false;
    }
    $argsChildren = self::getArgsUserChildren([...$args]);
    if (self::setChildrens($argsChildren) === false) {
      $_SESSION['message'] = 'Error al cargar o modificar datos de hijos del beneficiary con el CUIL' . $_SESSION['user_id'];
      return false;
    }
    return true;
  }

  private static function setUser($args)
  {
    if (self::searchUser($_SESSION['user_id']) !== null) {
      $queryBeneficiario = "UPDATE Beneficiary 
       SET lastname_beneficiary = ? , 
       name_beneficiary = ?, 
       CBU_beneficiary = ?, 
       type_account = ?, 
       email_beneficiary = ?
       WHERE user_id = ?;";
    } else {
      $queryBeneficiario = "INSERT INTO Beneficiary (lastname_beneficiary, name_beneficiary, CBU_beneficiary, type_account, email_beneficiary, user_id) VALUES (?, ?, ?, ?, ?, ?);";
    }
    return self::$instance->query($queryBeneficiario, $args);
  }

  private static function setChildren($args)
  {
    $args['name_children'] = strtolower($args['name_children']);
    $band = self::searchChildren([$_SESSION['user_id'], $args['name_children']]);
    $args['user_id'] = $_SESSION['user_id'];
    if ($band !== null) {
      $args['children_id'] = $band;
      $queryChildren = "UPDATE Children
     SET  url_file = ?,
     file_name = ?, 
     name_children = ?, 
     education_level = ?
     WHERE user_id = ? AND children_id = ?;";
    } else {
      $args['children_id'] = Uuid::uuid4()->toString();
      $queryChildren = "INSERT INTO Children (url_file, file_name, name_children, education_level, user_id, children_id) VALUES (?,?,?,?,?,?);";
    }
    return self::$instance->query($queryChildren, $args);
  }

  private static function setChildrens($args)
  {
    try {
      foreach ($args as $arg) {
        self::setChildren($arg);
      }
      return true;
    } catch (Exception $e) {
      return false;
    }
  }

  private static function getArgsUser($args)
  {
    $newArgs = [];
    foreach ($args as $key => $value) {
      if ($key !== 'childrens') {
        $newArgs[] = $value;
      }
    }
    $newArgs[] = $_SESSION['user_id'];
    return $newArgs;
  }

  private static function getArgsUserChildren($args)
  {
    $newArgs = [];

    foreach ($args['childrens']['value'] as $keyArg => $valueArg) {
      $newItem = [];

      foreach ($valueArg as $key => $value) {
        if (strpos($key, 'name-') !== false) {
          $newItem['name_children'] = $value;
        } else {
          $newItem[$key] = $value;
        }
      }

      // Agregar el nuevo elemento al arreglo resultante
      $newArgs[] = $newItem;
    }

    return $newArgs;
  }
}
