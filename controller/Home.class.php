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
        $insert = $this->model->insert(["UserID"=>"1", "ComicID"=>"1", "Rating"=>"10","Content"=>"very nice"]);
        $data =[
            "select"    => $this->model->select(),
            "insert"    => $insert,
            "join"      => $this->model->join(["Review"=>["ID","UserID","ComicID","Rating"],"Comic"=>["ComicName","Sinopsis","Genre"]]),
            "update"    => $this->model->update(["UserID"=>"2", "ComicID"=>"2", "Rating"=>"5","Content"=>"not so nice"], $insert),
            "delete"    => $this->model->delete($insert)
        ];
        $this->view->loadPage("home",$data);
    }
    
}