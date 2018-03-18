<?php
/**
 * Created by PhpStorm.
 * User: johangriesel
 * Date: 13052016
 * Time: 08:48
 * @package    ${NAMESPACE}
 * @subpackage ${NAME}
 * @author     johangriesel <info@stratusolve.com>
 */
?>
<!DOCTYPE html>
<html>
<head>
    <title>Basic Task Manager</title>
    <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.min.css">
</head>
<body>

<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Modal title</h4>
            </div>
            <div class="modal-body">
                
                    <div class="row">
                        <div class="col-md-12" style="margin-bottom: 5px;;">
                            <input id="InputTaskName" type="text" placeholder="Task Name" class="form-control">
                        </div>
                        <div class="col-md-12">
                            <textarea id="InputTaskDescription" placeholder="Description" class="form-control"></textarea>
                        </div>
                    </div>
                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button id="deleteTask" type="button" data-id="0" class="action btn btn-danger">Delete Task</button>
                <button id="saveTask" type="button" data-id="0" class="action btn btn-primary">Save changes</button>
            </div>
        </div>
    </div>
</div>


<div class="container-fluid">
    <div class="row">
        <div class="col-md-3">

        </div>
        <div class="col-md-6">
            <h2 class="page-header">Task List</h2>
            <!-- Button trigger modal -->
            <button id="newTask" type="button" class="btn btn-primary btn-lg" style="width:100%;margin-bottom: 5px;" data-toggle="modal" data-target="#myModal">
                Add Task
            </button>
            <div id="TaskList" class="list-group">
                <!-- Assignment: These are simply dummy tasks to show how it should look and work. You need to dynamically update this list with actual tasks -->
            </div>
        </div>
        <div class="col-md-3">

        </div>
    </div>
</div>
</body>
<script type="text/javascript" src="assets/js/jquery-1.12.3.min.js"></script>
<script type="text/javascript" src="assets/js/bootstrap.min.js"></script>
<script type="text/javascript">
    /*
     * i am sorry i actually couldnt deal with how this was written it as it was very over done and not very S.O.L.I.D.
     */
    
    //lets get the tasks 
    manageState();
     
     //lets listen and check if we should edit create or update a task
     $('.action').on('click',function(e){
         var action = $(this).attr('id');
         var id     = $(this).attr('data-id');
         var name   = '';
         var desc   = '';
         if(action !== 'delete'){
             name = $('#InputTaskName').val();
             desc = $('#InputTaskDescription').val();
         }
         $('#myModal').modal('hide');
         manageState(action,id,name,desc);
     });
     
    
     
     //core function to call crud
     function manageState(action = 'all',id = 0,name = 0,desc =0){
         
          $.post("../stratusolve/controller/gateKeeper.php", 
            {
                action :action,
                id:id,
                name:name,
                desc:desc
            }, 
            function(result){
                //first clean all inputs
                $('input,textarea').val('');
                $('#deleteTask').attr('data-id',0);
                $('#saveTask').attr('data-id',0);
                //then update the list
                $( "#TaskList" ).html(result); 
                
                //a lil to handle late static binding as live isnt available in the jquery we have in the test
                $('a.list-group-item').on('click',function(e){
                    var myid = $(this).attr('id');
                    $('#InputTaskName').val($('#'+myid+'-list-group-item-heading').html());
                    $('#InputTaskDescription').val($('#'+myid+'-list-group-item-text').html());
                    $('#deleteTask').attr('data-id',$(this).attr('id'));
                    $('#saveTask').attr('data-id',$(this).attr('id'));
                });
               
            });
            
     }
     
</script>
</html>