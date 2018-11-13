<?php

/* PHP Class for managing queries and connecting to database, part of MVC Framework
 * AUTHOR: Antony Acosta
 * LAST EDIT: 2018-11-12
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
        $this->builder->delete()->where($this->builder->tables[0]->pk(),$id);
        return $this->run("rowCount");
        
    }
    
    public function update(array $data, $id){ //array_assoc as $field=>$value
        $this->builder->update(array_keys($data));
        
        $this->builder->where($this->builder->tables[0]->pk(),$id);
        
        return $this->run("rowCount", $data);
    }

    public function setTable($table){
        
        $this->builder = new QueryBuilder($table);
        
        $cols = $this->run("fetchAll");
        
        $pk = array_filter($cols,function($e){
            return $e["Key"] == "PRI";
        });
        
        $this->builder->tables[0]->setPk($pk[0]['Field']);
           
        $this->builder->tables[0]->setFields(array_map(function($e){
            return $e["Field"];
        }, $cols));
        
        $this->builder->getFks(0);
        
        $cols = $this->run("fetchAll");
        
        $fkskeys = array_map(function($e){
            return $e["COLUMN_NAME"];
        },$cols);
        
        $fksvalues = array_map(function($e){
            return $e["REFERENCED_TABLE_NAME"];
        },$cols);
        
        $this->builder->tables[0]->setFks(array_combine($fkskeys,$fksvalues));
        
        
    }
    public function addTable(string $table){
        $index = $this->builder->addTable($table);
        
        $cols = $this->run("fetchAll");
        
        $pk = array_filter($cols,function($e){
            return $e["Key"] == "PRI";
        });
        
        $this->builder->tables[$index]->setPk($pk[0]["Field"]);
        $this->builder->tables[$index]->setFields(array_map(function($e){
            return $e["Field"];
        }, $cols));
        
        $this->builder->getFks($index);
        
        $cols = $this->run("fetchAll");
        
        $fkskeys = array_map(function($e){
            return $e["COLUMN_NAME"];
        },$cols);
        
        $fksvalues = array_map(function($e){
            return $e["REFERENCED_TABLE_NAME"];
        },$cols);
        
        $this->builder->tables[$index]->setFks(array_combine($fkskeys,$fksvalues));
        
        return $index;
    }
    
    public function join(array $fields, int $maintable, string $type = "inner"){
        $this->builder->select($fields);
        foreach(array_keys($fields) as $t){
            $this->builder->join($type, $t, $maintable);
        }
        echo $this->builder->query;
        return $this->run("fetchAll");
        
    }
    
}
