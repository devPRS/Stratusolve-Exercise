<?php

/* 
 * Created by Jacques Artgraven
 * 33a homestead, rivonia
 */
require('../model/task.class.php');

//this is just a basic handler. we would do something more of a router in a proper application so we accept paramaters from the url so we can create the beginnings of a restful app
//but for our needs this will do.

if(isset($_POST['action'])){
    $tasks = new Task();
    switch($_POST['action']){
        case 'deleteTask':   
            $result = $tasks->Delete($_POST['id']);
            break;
        case 'saveTask':
            //we can use the same task and just check id to see if we must create or update
            if($_POST['id'] != 0){
            $result = $tasks->Update($_POST['name'], $_POST['desc'], $_POST['id']);
            }else{
             $result = $tasks->Create($_POST['name'], $_POST['desc']);
            }
            break;
        case 'all':
            $result = $tasks->FindAll();
            break;
    }
     echo $result;
 
}else{
    echo 'turns out you are not a real boy and cant play with us';
}
