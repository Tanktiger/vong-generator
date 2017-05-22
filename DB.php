<?php

class Database {
    /**
     * @var mysqli
     */
    private $con;

    /**
     * @var string
     */
    private $charset = "utf8";

    /**
     * @return mysqli
     */
    public function getCon()
    {
        return $this->con;
    }

    /**
     * @return string
     */
    public function getCharset()
    {
        return $this->charset;
    }

    /**
     * @param string $charset
     */
    public function setCharset($charset)
    {
        $this->charset = $charset;
    }

    /**
     * Database constructor.
     * @param string $ip
     * @param string $username
     * @param string $password
     * @param string $database
     */
    public function __construct($ip = '127.0.0.1', $username = "root", $password = "", $database = "")
    {
        $this->connect($ip, $username, $password, $database);
    }

    /**
     * @param $ip
     * @param $username
     * @param $password
     * @param $database
     */
    public function connect($ip, $username, $password, $database)
    {
        //close db connection
        if (null !== $this->con && $this->con instanceof mysqli) {
            $this->con->close();
        }

        $this->con = new mysqli($ip, $username, $password, $database);
        $this->con ->set_charset($this->getCharset());

    }

    /**
     * @param $query
     * @return bool|mysqli_result|null
     */
    public function query($query)
    {
        try {
            return $this->con->query($query);
        } catch (Exception $e) {
//            echo PHP_EOL . "ERROR: " . PHP_EOL . $e->getMessage() . PHP_EOL;
//            echo PHP_EOL .  $e->getFile() . " on Line: " . $e->getLine() . PHP_EOL;
           return null;
        }

    }

    /**
     * @param $result
     * @return array
     */
    public function fetch_all($result)
    {
        for ($res = array(); $tmp = $result->fetch_assoc();) $res[] = $tmp;
        return $res;
    }
}