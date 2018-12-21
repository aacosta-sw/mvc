<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
*/

echo "<hr>";
echo "<pre>";
var_dump($data);
echo "</pre>";
echo "<hr>";


echo "SELECT (fetchAll)";
echo "<pre>";
var_dump($data['select']);
echo "</pre>";
echo "<hr>";


echo "INSERT (lastInsertId)";
echo "<pre>";
var_dump($data['insert']);
echo "</pre>";
echo "<hr>";


echo "JOIN with COMIC (fetchAll)";
echo "<pre>";
var_dump($data['join']);
echo "</pre>";
echo "<hr>";

echo "UPDATE (rowCount)";
echo "<pre>";
var_dump($data['update']);
echo "</pre>";
echo "<hr>";


echo "DELETE (rowCount)";
echo "<pre>";
var_dump($data['delete']);
echo "</pre>";
