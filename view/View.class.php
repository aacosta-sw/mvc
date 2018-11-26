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
        $this->sources = __DIR__."/".$config['sources'];
        
    }
    
    public function setPageStructure(string $page, $struct=[])
    {
        if($struct === []){
            if(file_exists("{$this->sources}{$page}.struct.json")){
                $file = file_get_contents("{$this->sources}{$page}.struct.json");
            }
            $this->structs[$page] = json_decode($file);
        }
        else{
            $this->structs[$page] = $struct;
        }
    }
    
    public function loadPage($page,$data)
    {
        if(!isset($this->structs[$page])){
            $this->setPageStructure($page);           
        }
        foreach($this->structs[$page]->headers as $p){
            if($p !== ""){
                include_once "{$this->sources}{$p}";
            }
        }
        foreach($this->structs[$page]->pages as $p){
            if($p !== ""){
                include_once "{$this->sources}{$p}";
            }
        }
        foreach($this->structs[$page]->footers as $p){
            if($p !== ""){
                include_once "{$this->sources}{$p}";
            }      
        }
    }
    
    /*
        $page.struct.json format:
     * {
     *  headers:[file1.php,file2,php,file3.php]
     *  pages:[file4.php,file5.php]
     *  footers:[file6.php]
     * }
     *      

   
     * Page sources in  $this->sources folder   */
    
}

