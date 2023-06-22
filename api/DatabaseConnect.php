<?php

class DatabaseConnect
{
    private $server = 'localhost';
    private $dbname = 'Stardust';
    private $user = 'Galen';
    private $pass = 'kMfp0~456';

    public function connect()
    {
        $conn = mysqli_connect($this->server, $this->user, $this->pass, $this->dbname);

        if ($conn->connect_error) {
            die('Database Error:' . $conn->connect_error);
        } else {
            $conn->set_charset('utf8');
        }

        return $conn;
    }
}
