<?php
    
    namespace Skeleton\Database;

    require_once('database_config.php');

    class Database {
        
    	private $conn;

    	public function __construct() {
	        $this->conn = new \PDO(DSN,USER,PASSWORD);
    	}

        public function insert($table,$fields,$values) {
            $format = 'INSERT INTO %s (%s) VALUES (:%s)';
            $names = implode(',',$fields);
            $params = implode(',:',$fields);
            $query = sprintf($format,$table,$names,$params);
            $prep = $this->conn->prepare($query);
            $n = count($fields);
            for ($i=0; $i < $n; $i++) { 
                $prep->bindParam(':'.$fields[$i], $values[$i]);
            }
            $prep->execute();
        }

        public function select() {}

        public function update() {}

    }

?>