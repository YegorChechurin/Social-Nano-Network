<?php
    
    namespace Skeleton\Database;

    /**
     * A class which holds a database connection and wraps database queries.
     */
    class Database {
        
        /** 
         * @var PDO - Holds a database connection 
         */
    	private $conn;

        /**
         * Creates PDO database connection
         *
         * DSN, USER, PASSWORD are constants which belong to Skeleton\Database 
         * namespace, and are declared in skeleton/Database/database_config.php
         */
    	public function __construct() {
	        $this->conn = new \PDO(DSN,USER,PASSWORD);
    	}

        /**
         * Performs INSERT sql operation by means of PDO prepared statement
         *
         * @param string $table - Name of database table where data is inserted in.
         * @param string[] $fields - Name of table fields where data is inserted in.
         * @param mixed[] $values - Data to be inserted. Each data value 
         * corresponds to a specific table field, thus data values should be 
         * stated in the order which matches the order table fields are stated in.
         */
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

        /**
         * Performs SELECT sql operation 
         *
         * By default performs a simple SELECT operation without a WHERE clause.
         * WHERE clause should be passed as a parameter and only in a form of 
         * prepared statement. 
         *
         * @param string $table - Name of database table data is selected from.
         * @param string[] $fields - Name of table fields data is selected from.
         * @param string $clause_exp - WHERE clause in a form of prepared 
         * statement. It does NOT contain the WHERE keyword. Example:
         * $clause_exp = 'field=:parameter';
         * @param mixed[] $clause_pars - Associative array, where keys are
         * named parameter placeholders and values are parameters from the 
         * prepared statement of WHERE clause. Illustration of how this array 
         * should look for a given WHERE clause: 
         * $clause_exp = 'field1=:param1 AND field2=:param2'
         * $clause_pars = [':param1'=>value_of_param1,':param2'=>value_of_param2]
         *
         * @return mixed[] - Array of associative arrays. Each associative array
         * corresponds to a row in the database table. In each associative array
         * keys are table field names and values are contents of the 
         * corresponding table cells.  
         */
        public function select($table, Array $fields, $clause_exp = null, Array $clause_pars = null) {
            if ($clause_exp==null) {
                $format = "SELECT %s FROM %s";
                $fields = implode(',',$fields);
                $query = sprintf($format,$fields,$table);
                $action = $this->conn->query($query);
                $result = $action->fetchAll(\PDO::FETCH_ASSOC);
            } else {
                $format = "SELECT %s FROM %s WHERE {$clause_exp}";
                $fields = implode(',',$fields);
                $query = sprintf($format,$fields,$table);
                $prep = $this->conn->prepare($query);
                foreach ($clause_pars as $key => $value) {
                    $prep->bindValue($key, $value);
                }
                $prep->execute();
                $result = $prep->fetchAll(\PDO::FETCH_ASSOC);
            }
            return $result;
        }

        public function perform_custom_select($table, Array $fields, $clause_exp, Array $clause_pars) {
            $format = "SELECT %s FROM %s {$clause_exp}";
            $fields = implode(',',$fields);
            $query = sprintf($format,$fields,$table);
            $prep = $this->conn->prepare($query);
            foreach ($clause_pars as $key => $value) {
                $prep->bindValue($key, $value);
            }
            $prep->execute();
            $result = $prep->fetchAll(\PDO::FETCH_ASSOC);
            return $result;
        }

        /**
         * Performs UPDATE sql operation by means of PDO prepared statement
         *
         * @param string $table - Name of database table where data is updated.
         * @param string[] $fields - Name of table fields where data is updated.
         * @param string $clause - WHERE clause in a form of prepared 
         * statement. It does NOT contain the WHERE keyword. Example:
         * $clause = 'field=:parameter';
         * @param mixed[] $parameter_map - Associative array, where keys are
         * named parameter placeholders and values are parameters from the 
         * query to be performed. Illustration of how this array should look 
         * for a given set of fields and WHERE clause: 
         * $fields = ['field1',field2];
         * $clase = 'field3=:par3 AND field4=:par4';
         * $parameter_map = [
         *     ':field1'=>value_to_be_written_into_field1, 
         *     ':field2'=>value_to_be_written_into_field2,
         *     ':par3'=>value_of_par3,
         *     ':par4'=>value_of_par4
         *];
         */
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

        /**
         * Performs DELETE sql operation by means of PDO prepared statement
         *
         * @param string $table - Name of database table where data is deleted.
         * @param string $clause - WHERE clause in a form of prepared 
         * statement. It does NOT contain the WHERE keyword. Example:
         * $clause = 'field=:parameter';
         * @param mixed[] $parameter_map - Associative array, where keys are
         * named parameter placeholders and values are parameters from the 
         * query to be performed. Illustration of how this array should look 
         * for a given WHERE clause: 
         * $clase = 'field1=:par1 AND field2=:par2';
         * $parameter_map = [
         *     ':par1'=>value_of_par1,
         *     ':par2'=>value_of_par2
         *];
         */
        public function delete($table, $clause, Array $parameter_map) {
            $format = "DELETE FROM %s WHERE {$clause}";
            $query = sprintf($format,$table);
            $prep = $this->conn->prepare($query);
            foreach ($parameter_map as $key => $value) {
                $prep->bindValue($key, $value);
            }
            $result = $prep->execute();
        }

    }