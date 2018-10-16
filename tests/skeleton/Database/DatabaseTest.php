<?php 

use PHPUnit\Framework\TestCase;
use PHPUnit\DbUnit\TestCaseTrait;
use Skeleton\Database\Database;
use const Skeleton\Database\DSN;
use const Skeleton\Database\USER;
use const Skeleton\Database\PASSWORD;
use const Skeleton\Database\SCHEMA;

require_once '/opt/lampp/htdocs/SNN/skeleton/Database/database_config.php';

class DatabaseTest extends TestCase {

    use TestCaseTrait;

    public function getConnection() {   
        $pdo = new PDO(DSN,USER,PASSWORD);
        $conn = $this->createDefaultDBConnection($pdo, SCHEMA);
        return $conn;
    }

    public function getDataSet() {
        return $this->createFlatXMLDataSet(dirname(__FILE__).'/unit_tests-seed.xml');
    }

    public function testInsert() {
        $table = 'unit_tests';
        $fields = ['id','content','user','created'];
        $values = [3,'test','yegor','now'];
        $db = new Database();
        $db->insert($table,$fields,$values);
        $queryTable = $this->getConnection()->createQueryTable($table,'SELECT * FROM '.$table);
        $expectedTable = $this->createFlatXmlDataSet(dirname(__FILE__).'/expectedInsert.xml')
                              ->getTable($table);
        $this->assertTablesEqual($expectedTable, $queryTable);
    }

}


    // class DatabaseTest extends TestCase {

    //     public function testConnectionIsCreated() {
    //         $database = new Database();
    //         $connection = $database->conn;
    //         $pdo = new PDO('sqlite::memory:');
    //         $this->assertTrue(gettype($connection)=='object');
    //     }

    //     public function testSprintf() {
    //         $table = 'table';
    //         $fields = ['first', 'second', 'third'];
    //         $values = [1,2,3];
    //         $goal = 'INSERT INTO table (first,second,third) VALUES (:first,:second,:third)';
    //         $database = new Database();
    //         $test = $database->insert($table,$fields,$values);
    //         $this->assertTrue($test==$goal);
    //     }

    // }

?>