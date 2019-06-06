<?php

class DbConnectManager
{
    private $dsn;
    private $user;
    private $pass;
    private $dbh = NULL;

    function __construct($dsn, $user, $pass)
    {
        $this->dsn = $dsn;
        $this->user = $user;
        $this->pass = $pass;
        $this->connect();
    }

    private function connect()
    {
        try {
            $this->dbh = new PDO($this->dsn, $this->user, $this->pass);
        } catch (PDOException $e) {
            print "Error!:" . $e->getMessage() . "<br/>";
            die();
        }
    }

    public function getdbh()
    {
        return $this->dbh;
    }
}

?>