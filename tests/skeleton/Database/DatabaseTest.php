<?php 

use PHPUnit\Framework\TestCase;
use PHPUnit\DbUnit\TestCaseTrait;
use Skeleton\Database\Database;
use const Skeleton\Database\DSN;
use const Skeleton\Database\USER;
use const Skeleton\Database\PASSWORD;
use const Skeleton\Database\SCHEMA;

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
        $queryTable = $this->getConnection()->createQueryTable($table,"SELECT * FROM {$table}");
        $expectedTable = $this->createFlatXmlDataSet(dirname(__FILE__).'/expectedInsert.xml')
                              ->getTable($table);
        $this->assertTablesEqual($expectedTable, $queryTable);
    }

    public function testSelect() {
        $table = 'unit_tests';
        $fields = ['content'];
        $clause = 'id=:id';
        $clause_pars = [':id'=>2];
        $db = new Database();
        $crude_outcome = $db->select($table,$fields,$clause,$clause_pars);
        $outcome = $crude_outcome[0]['content'];
        $expected_outcome = 'I like it!';
        $this->assertTrue($outcome==$expected_outcome);
        $clause = 'user=:user';
        $clause_pars = [':user'=>'nancy'];
        $crude_outcome = $db->select($table,$fields,$clause,$clause_pars);
        $outcome = $crude_outcome[0]['content'];
        $expected_outcome = 'I like it!';
        $this->assertTrue($outcome==$expected_outcome);
    }

    public function testUpdate() {
        $table = 'unit_tests';
        $fields = ['content','created'];
        $clause = 'id=:id AND user=:user';
        $map = [
            ':id'=>1, ':user'=>'joe', ':content'=>'UPDATED', 
            ':created'=>'right now'
        ];
        $db = new Database();
        $db->update($table,$fields,$clause,$map);
        $queryTable = $this->getConnection()->createQueryTable($table,"SELECT * FROM {$table}");
        $expectedTable = $this->createFlatXmlDataSet(dirname(__FILE__).'/expectedUpdate.xml')
                              ->getTable($table);
        $this->assertTablesEqual($expectedTable, $queryTable);
    }

}

?>