<?php

class DatabaseConnect
{
    private $server = 'localhost';
    private $dbname = 'amatoscar';
    private $user = 'root';
    private $pass = 'mbcroot';

    public function connect()
    {
        try {
            $conn = new PDO('mysql:host=' . $this->server . ';dbname=' . $this->dbname, $this->user, $this->pass);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $conn;
        } catch (\Exception $e) {
            echo "Database Error:" . $e->getMessage();
        }
    }
}
