<?php
/* PHP Class for managing views, part of MVC Framework
 * AUTHOR: Antony Acosta
 * LAST EDIT: 2018-11-19
 */

class View {
    
    private $vendor;
    private $sources;
    private $structs;
    
    public function __construct($config = null)
    {
        if(is_null($config)){
            $config = parse_ini_file("config.ini");
        } 
        $this->vendor = $config['vendor'];
        $this->sources = $config['sources'];
        
    }
    
    public function setPageStructure($struct)
    {
        if(is_array($struct)){
            $this->structs[] = $struct; 
        }
   
        elseif(is_string($struct)){
            if(file_exists($struct)){
                $file = file_get_contents($struct);
            }
            
            elseif(file_exists("{$struct}.json")){
                $file = file_get_contents("{$struct}.json");
            }
            $this->structs[] = json_decode($file);
        }
    }
    
    public function loadPage($page)
    {
        if(isset($this->structs[$page])){
            foreach($this->structs[$page] as $p){
                include_once "{$this->sources}{$page}/{$p}";
            }
        }
        else{
            include_once "{$this->sources}{$page}/index.php";
        }
    }
    
    
    
}

