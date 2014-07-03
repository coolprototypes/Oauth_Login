<?php

class LoginDbConnect {

    private $conn;

    function __construct() {
        
    }

    /**
     * Establishing database connection
     * @return database connection handler
     */
    function connect() {
        require_once 'server-libs/NotORM.php';
        require_once 'config.php';
        $dsn = 'mysql:dbname=' . DB_NAME . ';host=' . DB_HOST;
        $pdo = new PDO($dsn, DB_USERNAME, DB_PASSWORD);
        $this->conn = new NotORM($pdo);

        // returing connection resource
        return $this->conn;
    }

}
