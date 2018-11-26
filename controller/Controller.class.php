
<?php  
/* PHP Class for managing views and models
 * AUTHOR: Mickael Souza, modified by Antony Acosta for working with current version of MVC
 * LAST EDIT: 2018-11-26
 */

class Controller{	 
    
    protected $model;
    protected $view;
   
    public function __construct()
    {
        $this->view = new View();
        $this->model = new Model();
    }
}