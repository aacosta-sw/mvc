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
        $this->vendor = "view/".$config['vendor'];
        $this->sources = __DIR__."/".$config['sources'];
        
    }
    
    public function setPageStructure(string $page, $struct=[])
    {
        if($struct === []){
            if(file_exists("{$this->sources}{$page}.struct.json")){
                $file = file_get_contents("{$this->sources}{$page}.struct.json");
            }elseif(file_exists("{$this->sources}{$page}.json")){
                $file = file_get_contents("{$this->sources}{$page}.json");
            }
            $this->structs[$page] = json_decode($file);
        }
        else{
            $this->structs[$page] = $struct;
        }
    }
    
    public function loadPageFromStruct($page,$data)
    {
        if(!isset($this->structs[$page])){
            $this->setPageStructure($page);           
        }
        $data["title"] = $this->structs[$page]->title; // carrega title do structs
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
    
    public function link($data){
        // [class, method, param]
        //to be modified later on, when router gets improved for working better with mvc
        $link = $_SERVER["SCRIPT_NAME"]."?q=/".implode("/",$data);
        return $link;
        
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

