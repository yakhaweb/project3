<?php

if($_SERVER["REQUEST_METHOD"]=="POST"){
    define('admin23', true);
    include('db_connect.php');
    include('../functions/functions.php');
    
    $email=clear_string($_POST["email"]);
    
    
    if($email!=""){
       $result = mysql_query("SELECT email FROM `reg_user` WHERE email='$email'",$link); 
        if(mysql_num_rows($result)>0){
            //генирация пароля
            $newpass=fungenpass();
            //шифрование пароля
            $pass=md5($newpass);
            $pass=strrev($pass);
            $pass= strtolower("9nm2rv8q".$pass."2yo6z");
            
            //обновление пароля на новый
            $update=mysql_query("UPDATE `reg_user` SET pass='$pass' WHERE email='$email'",$link);
            
            //отправка нового пароля
            
            send_mail ('noreply@shop.ru',
            $email,
            'Новый пароль для сайта avto-mag.ru',
            'Ваш пароль: '.$newpass);
        
        echo 'yes';    
        }
        else{
            echo 'Данный E-mail не найден!';
        }   
    }
    else{
        echo 'Укажите свой E-mail';
    }
}
      
?>