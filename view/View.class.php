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
        $this->vendor = __DIR__.$config['vendor']; //common assets (Ex: Bootstrap, jquery, plugins, etc)
        $this->sources = __DIR__."/".$config['sources']; //sources where html files are
        $this->useSingleFileStruct = $config['useSingleFileStruct']; //search for files or for indexes in view/structs.json      
    }
    
    public function setPageStructure(string $page, $struct=[])
    {   
        try{
        if($struct === []){
            if($this->useSingleFileStruct){
                if(!file_exists(__DIR__."/structs.json")){
                    throw new Exception("Invalid option: ".__DIR__."/structs.json does not exists and useSingleFileStruct is set to true in config.ini");
                }
                $file = json_decode(file_get_contents(__DIR__."/structs.json"));
                $structpath = explode("_", $page);
                $this->structs[$page] = (count($structpath) < 2) ? $file->{$structpath[0]} : $file->{$structpath[0]}->{$structpath[1]};
                
            }else{
                if(file_exists("{$this->sources}{$page}.struct.json")){
                    $file = file_get_contents("{$this->sources}{$page}.struct.json");
                }elseif(file_exists("{$this->sources}{$page}.json")){
                    $file = file_get_contents("{$this->sources}{$page}.json");
                }else{
                    throw new Exception("Invalid parameter, {$page}.struct.json does not exist, neither {$page}.json");
                }
                $this->structs[$page] = json_decode($file);
            }
                
        }
        else{
            $this->structs[$page] = $struct;
        }
        }catch(Exception $e){
            die("Caught Exception [".$e->getMessage()."] while defining struct for {$page}, please fix that to continue");
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

