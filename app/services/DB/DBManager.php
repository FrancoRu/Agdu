<?php

interface DatabaseInterface
{
    public function connect();
}

class MySQLDatabase implements DatabaseInterface
{
    private $PORT;
    private $SERVER;
    private $DATABASE;
    private $USERNAME;
    private $PASSWORD;

    public function __construct()
    {
        $this->PORT = $_ENV['DB_PORT'];
        $this->SERVER = $_ENV['DB_SERVER'];
        $this->DATABASE = $_ENV['DB_DATABASE'];
        $this->USERNAME = $_ENV['DB_USERNAME'];
        $this->PASSWORD = $_ENV['DB_PASSWORD'];
    }

    public function connect()
    {
        try {
            $conn = new mysqli($this->SERVER, $this->USERNAME, $this->PASSWORD, $this->DATABASE, $this->PORT);
            return $conn;
        } catch (Exception $e) {
            die('Connection failed: ' . $e->getMessage());
        }
    }
}

class DBManagerFactory
{
    private static $instance;

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function createDatabase()
    {
        return new MySQLDatabase();
    }
}
