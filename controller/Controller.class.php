
<?php  
/* PHP Class for managing views and models
 * AUTHOR: Mickael Souza, modified by Antony Acosta for working with current version of MVC
 * LAST EDIT: 2018-12-21
 */

class Controller{	 
    
    protected $model;
    protected $view;
    protected $router;
   
    public function __construct($collector = null, $inipath = __DIR__."/config.ini")
    {
        $config = ["View"=>null, "Model"=>null];
        if(file_exists($inipath)){
            $ini = parse_ini_file($inipath, 1);
            $config["View"] = (isset($ini["View"])) ? $ini["View"] : $config["View"];
            $config["Model"] = (isset($ini["Model"])) ? $ini["Model"] : $config["Model"];
        }
        $this->view = new View($config["View"]);
        $this->model = new Model($config["Model"]);
        if($collector !== null && $collector instanceof Collector){
            $this->router = new Router($collector);
        }
    }
    
    public function dispatch($query){
        $return = $this->router->resolve($query);
        extract($return); //cria variaveis com os nomes das chaves do array (Ex: $return['data"=>20] vira $data = 20 )

        // echo "<pre>";
        // var_dump($return);
        // echo "</pre>";
        // die();
        
        $this->view->loadPageFromStruct($load, $data);
    }
	
	public function redirect($to){
        if(is_array($to)){
            $link = $this->view->link($to);
        }else{
            $link = $_SERVER["SCRIPT_NAME"]."?q={$to}";
        }

        header("Location:".$link);
    }
}