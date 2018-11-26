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
        $this->model->setTable("Review");
        $this->model->addTable("Comic");
    }
    
    public function index()
    {   
        $data =[
            "select"    => $this->model->select(),
            "insert"    => $this->model->insert(["UserID"=>"1", "ComicID"=>"1", "Rating"=>"10","Content"=>"very nice"]),
            "join"      => $this->model->join(["Review"=>["ID","UserID","ComicID","Rating"],"Comic"=>["ComicName","Sinopsis","Genre"]]),
        ];
        $data[]= [
            "update"    => $this->model->update(["UserID"=>"2", "ComicID"=>"2", "Rating"=>"5","Content"=>"not so nice"], $data["insert"]),
            "delete"    => $this->model->delete($data["insert"])
        ];
        $this->view->loadPage("home",$data);
    }
    
}