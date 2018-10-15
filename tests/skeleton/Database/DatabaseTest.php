<?php 

use PHPUnit\Framework\TestCase;
use PHPUnit\DbUnit\TestCaseTrait;
use Skeleton\Database\Database;

// class DatabaseTest extends TestCase
// {
//     use TestCaseTrait;

//     /**
//      * @return PHPUnit\DbUnit\Database\Connection
//      */
//     public function getConnection()
//     {
//         //$pdo = new PDO('sqlite::memory:');
//         $dsn = "mysql:dbname=snn;host=localhost";
//         $user="root";
//         $password="";
//         $pdo = new PDO($dsn,$user,$password);
//         $conn = $this->createDefaultDBConnection($pdo, 'snn');
//         return $conn;
//     }

//     *
//      * @return PHPUnit\DbUnit\DataSet\IDataSet
     
//     public function getDataSet()
//     {
//         return $this->createFlatXMLDataSet(dirname(__FILE__).'/guestbook-seed.xml');
//     }
    
//     /**
//      * 
//      */
//     public function testConnectionIsCreated(){
//         $database = new Database();
//         $connection = $database->conn;
//         $pdo = new PDO('sqlite::memory:');
//         $this->assertInstanceOf(var_dump($pdo),var_dump($connection));
//     }

// }


    class DatabaseTest extends TestCase {

        public function testConnectionIsCreated() {
            $database = new Database();
            $connection = $database->conn;
            $pdo = new PDO('sqlite::memory:');
            //$this->assertInstanceOf($pdo,$connection);
            $this->assertTrue(gettype($connection)=='object');
        }

    }

?>