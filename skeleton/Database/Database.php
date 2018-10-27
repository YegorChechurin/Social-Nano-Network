<?php
    
    namespace Skeleton\Database;

    class Database {
        
    	private $conn;

    	public function __construct() {
	        $this->conn = new \PDO(DSN,USER,PASSWORD);
    	}

        public function insert($table, Array $fields, Array $values) {
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

        public function select($table, Array $fields, $clause_exp, Array $clause_pars) {
            $format = "SELECT %s FROM %s WHERE {$clause_exp}";
            $fields = implode(',',$fields);
            $query = sprintf($format,$fields,$table);
            $prep = $this->conn->prepare($query);
            foreach ($clause_pars as $key => $value) {
                $prep->bindParam($key, $value);
            }
            $prep->execute();
            $result = $prep->fetchAll(\PDO::FETCH_ASSOC);
            return $result;
        }

        public function update($table, Array $fields, $clause, Array $parameter_map) {
            $format = "UPDATE %s SET %s WHERE {$clause}";
            $param_fields = [];
            foreach ($fields as $field) {
                $param_fields[] = $field.'=:'.$field;
            }
            $structure = implode(',',$param_fields);
            $query = sprintf($format,$table,$structure);
            $prep = $this->conn->prepare($query);
            foreach ($parameter_map as $key => $value) {
                $prep->bindValue($key, $value);
            }
            $result = $prep->execute();
        }

    }

?>