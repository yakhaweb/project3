<?php
// обработчик кнопки нравится
if($_SERVER["REQUEST_METHOD"]=="POST"){
session_start();
if($_SESSION['likeid'] != (int)$_POST["id"]){
    define('admin23', true);
    include('db_connect.php');
    
    
    $id=(int)$_POST["id"];
   
  	$result = mysql_query("SELECT * FROM `table-products` WHERE `products_id`='$id'",$link);
    If (mysql_num_rows($result) > 0){
        $row=mysql_fetch_array($result);
        $new_count=$row["yes_like"]+1;
        $update=mysql_query("UPDATE `table-products` SET `yes_like`='$new_count' WHERE `products_id`='$id'",$link);
        echo $new_count;
        }
        $_SESSION['likeid']=(int)$_POST["id"];
        }
        else{
          echo 'no';
        }
        }