<?php  
/* PHP Class for building SQL queries
 * AUTHOR Mickael Braz de Souza, Modified by Antony Acosta 
 * LAST EDIT: 2018-11-08
 */

class QueryBuilder 
{
    public $query = "";
    public $tables = [];
    
    public function __construct($primarytable)
    {
        if($primarytable instanceof Table){
            $this->tables[0] = $primarytable;
        }else{
            $this->tables[0] = new Table($primarytable);
        }
        
        $this->getFields(0);
    }

    public function select(array $fields = [], $tables = [0]) //fields is a bidimensional array in format tableindex=>Array[fields]
    {   
        if(count($tables) == 1){
            $table = $tables[0];
            
            if(array_key_exists($table, $fields)){
               $fields = $fields[$table]; 
            }
            $this->query = "SELECT ".(($fields) ? implode(", ",$fields) : "*")." FROM {$this->tables[$table]->name} ";
            
        }elseif(count($tables > 1)){
            $stringfields = "";
            
            foreach($tables as $table){
                $currentTableFields = array_intersect($fields[$table],$this->tables[$table]->fields);
                
                if($currentTableFields){
                    $currentTableFields = array_map(function($e){
                        return "{$this->tables[$table]->name}.{$e}";
                    }, $currentTableFields);
                    
                    $stringfields.= implode(", ",$currentTableFields);
                }else{
                    $stringfields.= " {$this->tables[$table]->name}.* ";
                }                
            }
            
            $this->query = "SELECT {$stringfields} FROM {$this->tables[$table[0]]->name}"; 
            //$table[0] is always the main table
            //STILL NEEDS TO DO JOIN
        }
        
        return $this;
    }
    
    public function insert(array $data, int $table = 0)
    {
        //check valid fields
        $data = array_intersect($this->tables[$table]->fields, $data);
        
        $this->query = "INSERT INTO {$this->tables[$table]->name} ";

        $this->query.="(".implode(", ", $data).")";

        $this->query.= " VALUES ";

        $doubledoot = array_map(function($e){
            return ":{$e}";
        }, $data);

        $this->query.="(".implode(", ", $doubledoot).")";

        return $data;
    }

    public function update(array $data, int $table = 0)
    {
        $this->query = "UPDATE {$this->tables[$table]->name} SET ";

        //check valid fields
        $fields = array_intersect($this->tables[$table]->fields, $data);
        
        foreach($fields as $field) {
                $this->query.="{$field} = :{$field}, ";
        }

        $this->query = substr($this->query, 0, -2);

        return $this;
    }

    public function delete(int $table = 0)
    {
        $this->query = "DELETE FROM {$this->tables[$table]->name}";

        return $this;
    }
    
    public function where($field, $value, int $table = 0, string $operator = "=")
    {
        if($this->tables[$table]->field($field) && $this->tables[$table]->pk() !== $field)
        {   echo "<pre>";
            var_dump($this->tables[$table]->fields);
            echo "</pre>";
            return false;
        }
        
        $this->query.= " WHERE {$this->tables[$table]->name}.{$field} {$operator} {$value}";
        
        return $this;
    }
    
    public function join(string $type, int $table2, int $table1 = 0)
    {   //makes a $type join takin $table1's fk that references $table2's pk
        $type = strtoupper($type); 
        
        $fk = $this->tables[$table1]->name . $this->tables[$table1]->fk($this->tables[$table2]->name );
        
        $pk = $this->tables[$table2]->name . $this->tables[$table2]->pk();
        
        $this->query.= " {$type} JOIN {$this->tables[$table2]->name} ON {$fk} = {$pk}";
        
        return $this;
    }
    
    public function getFields(int $table) 
    {
        $this->query = "DESCRIBE {$this->tables[$table]->name}";     
        
        return $this;
    }
    
    public function getFks(int $table)
    {
            $this->query = "SELECT REFERENCED_TABLE_NAME, COLUMN_NAME "
            . "FROM information_schema.KEY_COLUMN_USAGE "
            . "WHERE REFERENCED_TABLE_SCHEMA = TABLE_SCHEMA AND TABLE_NAME = '{$this->tables[$table]->name}'";
            return $this;
    }
    
    public function addTable($tablename)
    {
        if($tablename instanceof Table){
            $this->tables[] = $tablename;
        }else{
            $this->tables[] = new Table($tablename);
        }
        $tableindex = count($this->tables)-1;
        $this->getFields($tableindex);
        
        return $tableindex;
    }
    
}
