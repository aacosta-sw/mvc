<?php

/* PHP Class for managing queries and connecting to database, part of MVC Framework
 * AUTHOR: Antony Acosta
 * LAST EDIT: 2018-10-30
 */
class Model {
    
    private $builder;
    private $connection;
    
    public function __construct($config = null, $table = null) {
        if(!$config){
           $config = parse_ini_file("config.ini");
        }
        $this->connection = new Connection($config['user'],
                $config['password'],
                $config['dbname'],
                $config['host'],
                $config['charset']
                );
        if($table){
            $this->setTable($table);
        }
    }
    
    private function run($callback, $params = null){
        return $this->connection->exec($this->builder->query,$callback,$params);
    }
    
    
    public function select(array $fields = []){
        $this->builder->select($fields);
        return $this->run("fetchAll");
    }
    
    public function insert(array $data = []){ //array assoc as $field=>$value
        $validfields = $this->builder->insert(array_keys($data));
        $validfields = array_flip($validfields);
        $data = array_intersect_key($data, $validfields);
        return $this->run("lastInsertId",$data);
    }
    
    public function delete($id){
        $this->builder->delete($id);
        return $this->run("rowCount");
        
    }
    
    public function update(array $data, $whereId){ //array_assoc as $field=>$value
        $this->builder->update(array_keys($data));
        $this->builder->whereId($whereId);
        return $this->run("rowCount", $data);
    }
    

    public function setTable($table){
        $this->builder = new QueryBuilder($table);
        
        $cols = $this->connection->exec($this->builder->query, "fetchAll");
        
        $pk = array_filter($cols,function($e){
            return $e["Key"] == "PRI";
        });
        
        $this->builder->setPrimaryKey($pk[0]['Field']);
        
        $fks = array_filter($cols,function($e){
            return $e["Key"]=="MUL";
        });
        
        $this->builder->setForeignKeys(array_map(function($e){
            return $e["Field"];
        }, $fks));
        
        $this->builder->setFields(array_map(function($e){
            return $e["Field"];
        }, $cols));
        
    }
    
}
