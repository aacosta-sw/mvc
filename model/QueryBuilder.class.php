<?php  
/* PHP Class for building SQL queries
 * AUTHOR Antony Acosta
 * LAST EDIT: 2018-12-20
 */

class QueryBuilder 
{
    public $query = "";
    public $tables = [];
    public $primarytable = "";
    
    public function __construct($primarytable)
    {
        if($primarytable instanceof Table){
            $this->tables[$primarytable->name] = $primarytable;
            $this->primarytable = $primarytable->name;
        }else{
            $this->tables[$primarytable] = new Table($primarytable);
            $this->primarytable = $primarytable;
        }
        
        $this->getFields($primarytable);
    }

    //ESTE MÉTODO TEM QUE MORRER
    public function getTableIndex($name)
    {
        for($i=0; $i<count($this->tables); $i++){
            if($this->tables[$i]->name == $name){
                return $i;
            }
        }
    }
        
    public function select(array $fields = []) //fields is a bidimensional array in format table=>Array[fields] OR just array with field names of main table
    {   
        $parsedfields = [];
        if($fields){
             foreach($fields as $table=>$field){
                if(is_array($field)){

                    foreach($field as $f){
                        $parsedfields[] = "{$this->tables[$table]->name}.{$f}";
                    }

                }else{
                    $parsedfields[] = "{$this->tables[$this->primarytable]->name}.{$field}";
                }

            }
            $stringfields = implode($parsedfields, ", ");
        }else{
            $stringfields = "*";
        }
        
        $this->query = "SELECT {$stringfields} FROM {$this->tables[$this->primarytable]->name}"; 
        //STILL NEEDS TO DO JOIN
        return $this;
    }
    
    public function insert(array $data, string $table = "")
    {   
        //if table empty, set primary;
        $table = ($table === "") ? $this->primarytable : $table;
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

    public function update(array $data, string $table = "")
    {
        //if table empty, set primary;
        $table = ($table === "") ? $this->primarytable : $table;

        $this->query = "UPDATE {$this->tables[$table]->name} SET ";

        //check valid fields
        $fields = array_intersect($this->tables[$table]->fields, $data);
        
        foreach($fields as $field) {
                $this->query.="{$field} = :{$field}, ";
        }

        $this->query = substr($this->query, 0, -2);  

        return $this;
    }

    public function delete(string $table = "")
    {
        //if table empty, set primary;
        $table = ($table === "") ? $this->primarytable : $table;

        $this->query = "DELETE FROM {$this->tables[$table]->name}";

        return $this;
    }
    
    public function where($field, $value, string $operator = "=", string $table = "")
    {
        $table = ($table === "") ? $this->primarytable : $table;
        if($this->tables[$table]->field($field) && $this->tables[$table]->pk() !== $field)
        {  
            return false;
        }
        
        $this->query.= " WHERE {$this->tables[$table]->name}.{$field} {$operator} {$value}";
        
        return $this;
    }
    
    public function join(string $type,string $table2)
    {   //makes a $type join taking $table1's fk that references $table2's pk or vice-versa
        $type = strtoupper($type); 
        if($this->tables[$this->primarytable]->fk($this->tables[$table2]->name)){
            $fk = $this->tables[$this->primarytable]->name .".". $this->tables[$this->primarytable]->fk($this->tables[$table2]->name);
            $pk = $this->tables[$table2]->name .".". $this->tables[$table2]->pk();
        }elseif($this->tables[$table2]->fk($this->tables[$this->primarytable])){
            $fk = $this->tables[$table2]->name .".". $this->tables[$table2]->fk($this->tables[$this->primarytable]->name);
            $pk = $this->tables[$this->primarytable]->name .".". $this->tables[$this->primarytable]->pk();
        }else{
            return false;
        }
        
        $this->query.= " {$type} JOIN {$this->tables[$table2]->name} ON {$fk} = {$pk}";
        
        return $this;
    }
    
    public function getFields(string $table) 
    {
        $this->query = "DESCRIBE {$this->tables[$table]->name}";     
        
        return $this;
    }
    
    public function getFks(string $table)
    {
            $this->query = "SELECT REFERENCED_TABLE_NAME, COLUMN_NAME "
            . "FROM information_schema.KEY_COLUMN_USAGE "
            . "WHERE REFERENCED_TABLE_SCHEMA = TABLE_SCHEMA AND TABLE_NAME = '{$this->tables[$table]->name}'";
            return $this;
    }
    
    public function addTable($tablename)
    {
        if($tablename instanceof Table){
            $this->tables[$tablename->name] = $tablename;
        }else{
            $this->tables[$tablename] = new Table($tablename);
        }
        $this->getFields($tablename);
        
        return $this;
    }
    
}
