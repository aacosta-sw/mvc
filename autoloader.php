<?php
/* PHP Autoloader Function for loading classes, for using with MVC Framework
 * AUTHOR: Antony Acosta
 * LAST EDIT: 2018-11-26
 */

function autoloader($file){
    
    if(file_exists("{$file}.php")){
        include_once "{$file}.php";
        return true;
    }elseif(file_exists("{$file}.class.php")){
        include_once "{$file}.class.php";
        return true;
    }else{
        $dirh = opendir(getcwd());
        if ($dirh) {
            while (($dirElement = readdir($dirh)) !== false) {
                if(is_dir($dirElement)){
                    if(file_exists("{$dirElement}/{$file}.php")){
                        include_once "{$dirElement}/{$file}.php";
                        closedir($dirh);
                        return true;
                    }elseif(file_exists("{$dirElement}/{$file}.class.php")){
                        include_once "{$dirElement}/{$file}.class.php";
                        closedir($dirh);
                        return true;
                    }
                }
            }
            closedir($dirh);
            return false;
        }
    } 
}

spl_autoload_register("autoloader");