<?php

use Dotenv\Exception\InvalidFileException;

class FTPManagement
{
  private $SERVER;
  private $USERNAME;
  private $PASSWORD;
  private $CONNECT;

  private static $instance = null;

  private function __construct()
  {
    $this->SERVER = $_ENV['FTP_SERVER'];
    $this->USERNAME = $_ENV['FTP_USERNAME'];
    $this->PASSWORD = $_ENV['FTP_PASSWORD'];
    $this->CONNECT = $this->connect();
  }

  public static function getInstance()
  {
    if (self::$instance === null) {
      self::$instance = new self();
    }
    return self::$instance;
  }
  public function uploadFile($arg)
  {
    if (!$this->validateArg($arg)) {
      throw new InvalidArgumentException('Parámetros incorrectos.');
    }

    ftp_pasv($this->CONNECT, true);
    $userFolder = $arg['user_id'];
    $downloadFolder = "/public_html/subesc/files/constancias/$userFolder";
    $localFilePath = $arg['file_children'];

    if (!@ftp_chdir($this->CONNECT, $downloadFolder)) {
      ftp_mkdir($this->CONNECT, $downloadFolder);
      ftp_chmod($this->CONNECT, 0777, $downloadFolder);
    }

    $destination = $downloadFolder . '/' . $arg['name'];

    // Verificar si el archivo local existe
    if (!file_exists($localFilePath)) {
      throw new InvalidArgumentException("El archivo local no existe: $localFilePath");
    }

    $uploadResult = ftp_put($this->CONNECT, $destination, $localFilePath, FTP_BINARY);
    if (!$uploadResult) {
      $ftpError = error_get_last();
      throw new RuntimeException("Error al cargar el archivo a través de FTP: $destination");
    }


    if (!$uploadResult) {
      throw new RuntimeException("Error al cargar el archivo a través de FTP: $destination");
    }

    return $destination;
  }

  public function downloadFile($args)
  {
    $remoteFilePath = $args['filepath'];
    ftp_pasv($this->CONNECT, true);

    // Establecer el tipo de contenido y las cabeceras para la descarga
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . $args['filename'] . '"');

    // Obtener el tamaño del archivo desde el servidor FTP
    $fileSize = ftp_size($this->CONNECT, $remoteFilePath);
    header('Content-Length: ' . $fileSize);

    // Obtener el contenido del archivo desde el servidor FTP y enviarlo al navegador
    $fileContent = ftp_get($this->CONNECT, 'php://output', $remoteFilePath, FTP_BINARY);
    if (!$fileContent) {
      throw new InvalidFileException('Error al obtener el contenido del archivo');
    }

    exit;
  }


  private function transferFile($source, $destination)
  {
    $transferResult = ftp_get($this->CONNECT, $destination, $source, FTP_BINARY);

    if (!$transferResult) {
      throw new RuntimeException("Error al transferir el archivo: $source a $destination");
    }
  }

  private function validateArg($arg)
  {
    if (!isset($arg['user_id']) || !isset($arg['file_children']) || !isset($arg['name'])) {
      return false;
    }

    return $this->validateExtension($arg);
  }

  private function validateExtension($arg)
  {
    $extension = strtolower(pathinfo($arg['name'], PATHINFO_EXTENSION));
    $extensionAccepted = ['pdf', 'png', 'jpg', 'jpeg'];
    return in_array($extension, $extensionAccepted);
  }

  private function connect()
  {
    $ftpConnection = ftp_connect($this->SERVER);

    if (!$ftpConnection) {
      throw new RuntimeException("No se pudo conectar al servidor FTP");
    }

    $ftpLogin = ftp_login($ftpConnection, $this->USERNAME, $this->PASSWORD);
    if (!$ftpLogin) {
      throw new RuntimeException("Error al iniciar sesión en FTP");
    }

    return $ftpConnection;
  }
}
