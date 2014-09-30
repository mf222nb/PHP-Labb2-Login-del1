<?php
/**
 * Created by PhpStorm.
 * User: Martin
 * Date: 2014-09-26
 * Time: 12:08
 */
//Tog denna från Emils föreläsning https://github.com/dntoll/1dv408-HT14/blob/master/Portfolio/src/model/Repository.php
abstract class Repository {
    protected $dbUsername = 'root';
    protected $dbPassword = '';
    protected $dbConnstring = 'mysql:host=127.0.0.1;dbname=loginapp';
    protected $dbConnection;
    protected $dbTable;

    protected function connection() {
        if ($this->dbConnection == NULL)
            $this->dbConnection = new \PDO($this->dbConnstring, $this->dbUsername, $this->dbPassword);

        $this->dbConnection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        return $this->dbConnection;
    }
}