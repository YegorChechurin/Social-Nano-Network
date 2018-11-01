<?php

use PHPUnit\Framework\TestCase;
use PHPUnit\DbUnit\TestCaseTrait;
use Skeleton\Database\Database;
use Models\Messenger;
use const Skeleton\Database\DSN;
use const Skeleton\Database\USER;
use const Skeleton\Database\PASSWORD;
use const Skeleton\Database\SCHEMA;

class MessengerTest extends TestCase {

    use TestCaseTrait;

    public function getConnection() {   
        $pdo = new PDO(DSN,USER,PASSWORD);
        $conn = $this->createDefaultDBConnection($pdo, SCHEMA);
        return $conn;
    }

    public function getDataSet() {
        return $this->createFlatXMLDataSet(dirname(__FILE__).'/.xml');
    }

}

?>