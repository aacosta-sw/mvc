<?php  
/* PHP Class for building SQL queries
 * AUTHOR Mickael Braz de Souza, Modified by Antony Acosta 
 * LAST EDIT: 2018-10-30
 */

class QueryBuilder 
{
    public $query = "";
    private $table;
    private $primarykey;
    private $foreignkeys = [];   
    private $fields = [];

    public function __construct($table)
    {
        $this->table = $table;
        $this->getFields();
    }

    public function select(Array $fields = [], $exclude = false)
    {   
        if($exclude && $fields){
            $fields = array_diff($this->fields, $fields);
        }
        $this->query = "SELECT ".(($fields) ? implode(", ",$fields) : "*")." FROM {$this->table}";
        
        return $this;
    }

    public function insert(array $data)
    {
        //check valid fields
        $data = array_intersect($this->fields, $data);
        
        $this->query = "INSERT INTO {$this->table} ";

        $this->query.="(".implode(", ", $data).")";

        $this->query.= " VALUES";

        $doubledoot = array_map(function($e){
            return ":{$e}";
        }, $data);

        $this->query.="(".implode(", ", $doubledoot).")";

        return $data;
    }

    public function update(array $data)

    {
        $this->query = "UPDATE {$this->table} SET ";

        //check valid fields
        $fields = array_intersect($this->fields, $data);
        
        foreach($fields as $field) {
                $this->query.="{$field} = :{$field}, ";
        }

        $this->query = substr($this->query, 0, -2);

        return $this;
    }

    public function delete($id) 
    {
        $this->query = "DELETE FROM {$this->table} WHERE {$this->primarykey} = {$id}";

        return $this;
    }
    
    public function whereId($id)
    {
        $this->query.= " WHERE {$this->primarykey} = {$id}";
        
        return $this;
    }
    
    public function getFields() 
    {
        $this->query = "DESCRIBE {$this->table}";
        
        return $this;
    }
    
    public function setPrimaryKey(string $pk)
    {
        $this->primarykey = $pk;
        return $this;
    }
    
    public function setForeignKeys(array $fks)
    {   
        $this->foreignkeys = $fks;
        return $this;
    }

    public function setFields(array $fields)
    {
        $this->fields = array_filter($fields,function($e){
            return $e !== $this->primarykey;
        });
    }
}

