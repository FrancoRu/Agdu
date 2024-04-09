<?php

require_once 'DBManager.php';

class DBManagment
{
  private static $instance; // Instancia única
  private static $stmt; // Cambiado a estático
  private static $db;

  private function __construct()
  {
    // Constructor privado para evitar la creación de instancias externas
    self::$db = DBManagerFactory::getInstance()->createDatabase();
    self::$stmt = self::$db->connect(); // Establecer la conexión
  }

  public static function getInstance()
  {
    if (!self::$instance) {
      self::$instance = new self();
    }
    return self::$instance;
  }

  //Sanitizo los argumentos

  private static function cleanParam($args)
  {
    $newArgs = array();
    foreach ($args as $key => $arg) {
      $newArgs[$key] = mysqli_real_escape_string(self::$stmt, $arg);
    }

    return $newArgs;
  }

  //Transformo de un array asosiativo a uno comun
  //con el fin de poder usarlo en la funcion bind_param() de msqli
  //para lograr dinamismo

  private static function transformArray($args)
  {
    $newArgs = array();
    foreach ($args as $arg) {
      array_push($newArgs, $arg);
    }
    return $newArgs;
  }

  public static function query($query, $args = null)
  {
    try {

      $statement = self::$stmt->prepare($query);

      if (!$statement) {
        throw new Exception("Error en la preparación de la consulta SQL.");
      }
      if ($args !== null) {
        $cleanArgs = self::cleanParam($args);

        $types = self::mapTypes($args); //Crep un string dependiendo de la cantidad de datos a parametrizar

        $values = self::transformArray($cleanArgs); //Lo transformo en un array simple

        array_unshift($values, $types); //posiciono al frente el tipo de datos de la parametrizacion
        $statement->bind_param(...$values); //Preparo la parametrizacion
      }

      $statement->execute();  //Ejecuto la query

      return $statement; //Retorno el resultado, ya haya sido exitoso o no 

    } catch (Exception $e) {
      return $e->getMessage();
    }
  }

  private static function mapTypes($args)
  {
    $types = '';
    foreach ($args as $key => $values) {
      $types .= 's';
    }
    return $types;
  }
  private static function types($arg)
  {
    switch ($arg) {
      case 'temp_file':
        return 'b';
        break;
      default:
        return 's';
        break;
    }
  }
}
