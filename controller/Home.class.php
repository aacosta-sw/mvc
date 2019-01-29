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
        $insertdata = ["UserID"=>"1", "ComicID"=>"1", "Rating"=>"10","Content"=>"very nice"];
        $updatedata = ["UserID"=>"2", "ComicID"=>"2", "Rating"=>"5","Content"=>"not so nice"];
        $insert = $this->model->insert($insertdata)->run("lastInsertId", $insertdata);
        $data =[
            "select"    => $this->model->select()->run("fetchAll"),
            "insert"    => $insert,
            "join"      => $this->model->join(["Review"=>["ID","UserID","ComicID","Rating"],"Comic"=>["ComicName","Sinopsis","Genre"]])->run('fetchAll'),
            "update"    => $this->model->update($updatedata, $insert)->run("rowCount", $updatedata),
            "delete"    => $this->model->delete($insert)->run("rowCount"),
            "count"     => $this->model->query("SELECT COUNT(*) FROM Review", "fetchAll")
        ];
        return $data;
    }
    
}