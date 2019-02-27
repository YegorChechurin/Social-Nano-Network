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

    private $table = 'database_tests';

    private $conn;

    private $db;

    public function getConnection() { 
        $this->db = new Database();  
        $pdo = new PDO(DSN,USER,PASSWORD);
        $this->conn = $this->createDefaultDBConnection($pdo, SCHEMA);
        return $this->conn;
    }

    public function getDataSet() {
        return $this->createFlatXMLDataSet(dirname(__FILE__).'/DatabaseFixture.xml');
    }

    public function testInsert() {
        $fields = ['id','content','user','created'];
        $values = [3,'test','yegor','now'];
        $this->db->insert($this->table,$fields,$values);
        $queryTable = $this->conn->createQueryTable($this->table,"SELECT * FROM {$this->table}");
        $expectedTable = $this->createFlatXmlDataSet(dirname(__FILE__).'/expectedInsert.xml')
                              ->getTable($this->table);
        $this->assertTablesEqual($expectedTable, $queryTable);
    }

    public function testSelect() {
        $fields = ['*'];
        $outcome = $this->db->select($this->table,$fields);
        $expected_outcome = [
            ['id'=>1, 'content'=>'Hello buddy!', 'user'=>'joe', 'created'=>'2010-04-24 17:15:23'],
            ['id'=>2, 'content'=>'I like it!', 'user'=>'nancy', 'created'=>'2010-04-26 12:14:20']
        ];
        $this->assertEquals($expected_outcome,$outcome);
        $fields = ['content'];
        $clause = 'id=:id';
        $clause_pars = [':id'=>2];
        $crude_outcome = $this->db->select($this->table,$fields,$clause,$clause_pars);
        $outcome = $crude_outcome[0]['content'];
        $expected_outcome = 'I like it!';
        $this->assertTrue($outcome==$expected_outcome);
        $clause = 'user=:user AND id=:id';
        $clause_pars = [':user'=>'nancy',':id'=>2];
        $crude_outcome = $this->db->select($this->table,$fields,$clause,$clause_pars);
        $outcome = $crude_outcome[0]['content'];
        $expected_outcome = 'I like it!';
        $this->assertTrue($outcome==$expected_outcome);
    }

    public function testUpdate() {
        $fields = ['content','created'];
        $clause = 'id=:id AND user=:user';
        $map = [
            ':id'=>1, ':user'=>'joe', ':content'=>'UPDATED', 
            ':created'=>'right now'
        ];
        $this->db->update($this->table,$fields,$clause,$map);
        $queryTable = $this->conn->createQueryTable($this->table,"SELECT * FROM {$this->table}");
        $expectedTable = $this->createFlatXmlDataSet(dirname(__FILE__).'/expectedUpdate.xml')
                              ->getTable($this->table);
        $this->assertTablesEqual($expectedTable, $queryTable);
    }

    public function testDelete() {
        $clause = 'id=:id AND user=:user';
        $map = [':id'=>1, ':user'=>'joe'];
        $this->db->delete($this->table,$clause,$map);
        $queryTable = $this->conn->createQueryTable($this->table,"SELECT * FROM {$this->table}");
        $expectedTable = $this->createFlatXmlDataSet(dirname(__FILE__).'/expectedDelete.xml')
                              ->getTable($this->table);
        $this->assertTablesEqual($expectedTable, $queryTable);
    }

}