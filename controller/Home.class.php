<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Home extends Controller{
    
    public function __construct()
    {
        parent::__construct();
        $this->model->setTable("itens");
    }
    
    public function index()
    {   
        $insertdata = ["nome"=>"Asdemiro Rodrigues", "turma"=>"SER2", "descricao"=>"de lorem ipsum"];
        $updatedata = ["turma"=>"info4"];
        $insert = $this->model->insert($insertdata)->run("lastInsertId", $insertdata);
        $data =[
            "select"    => $this->model->select()->run("fetchAll"),
            "insert"    => $insert,
            "update"    => $this->model->update($updatedata, $insert)->run("rowCount", $updatedata),
            "delete"    => $this->model->delete($insert)->run("rowCount"),
            "count"     => $this->model->query("SELECT COUNT(*) FROM itens", "fetchAll")
        ];
        return $data;
    }
    
}