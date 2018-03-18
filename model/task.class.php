<?php
/**
 * This class handles the modification of a task object
 */
class Task {
    public $TaskId;
    public $TaskName;
    public $TaskDescription;
    public $CurrentTask;
    public $database;
    protected $appName = "/stratu";
    protected $TaskDataSource;
    
    public function __construct($Id = null) {
        //so i am using a path for database based on the doc root and i need to add the app name.
        //if your document root is pointed to inside your folder based on the vhost then you can skip the app name or just make it / depending on your config
        //there was a bunch of tests here for creating but since we already handle this elsewhere i removed it and did a few changes to make the end result a bit more S.O.L.I.D
        //although given more time i would have done a more TDD approach and greated a unit test file
        $this->database = $_SERVER['DOCUMENT_ROOT'].$this->appName.'/database/Task_Data.txt';
    }
    
    public function Create($TaskName = "",$TaskDescription = "") {
        //i could have just passed the vars over for the purpose of staying oo we will do this
        $this->TaskId           = $this->getUniqueId();
        $this->TaskName         = $TaskName;
        $this->TaskDescription  = $TaskDescription;
        //then pass to the process file func
        $add = $this->ProcessFile('add');
        //finally save and return results
        return $this->Save($add);
        
    }
    
    protected function getUniqueId() {
        //there are many ways to do this but since uniqid does its based on unix time we will always have a unique character set
        $uniqueId = uniqid();
        return $uniqueId; 
    }
    
    public function Save($data) {
        //Assignment: Code to save task here
        $dataj = json_encode($data);
        file_put_contents($this->database,$dataj);
        
        //lets return the update list
        return  $this->MakeHtml($data);
    }
    
    public function Delete($id) {
        //Assignment: Code to delete task here
       $data = $this->ProcessFile('delete', $id);
       //update our db and return resultset
        return $this->Save($data);
    }
    
    public function FindAll(){
        //get all entries
        $TasksArray = $this->ProcessFile();
        //then show them to the front
        $result = $this->MakeHtml($TasksArray);
        return $result;
    }
    
    public function Update($name,$description,$id) {
        //Addition: since we have the id we can just update that item specific and then store the sheet
        $this->TaskName             = $name;
        $this->TaskDescription      = $description;
        //run the func with its new params
        $data = $this->ProcessFile('update', $id);
        //write and return results
        return $this->Save($data);
    }
    
    private function ProcessFile($action = null,$id = null){
        //no matter the function we are basicly doing the same loop through the array to add update read or write so why not hook into the same function to handle it all based on what has been passed in
        // this way adding new features is as simple as extending the case statement and adding the function
        
        //in a real word application we can also at this point do some proper security checks on the incoming data here
        
        //write now i created a database folder to hold the txt file. this we can then given this logic replace it with a function that can be a database adapter intead but for now this will do
            $this->TaskDataSource = file_get_contents($this->database);
            // lets decode the json
            $decode = json_decode($this->TaskDataSource);
            // lets loop over it quick and return the one that matches the id we passed in
            
            $newFile = array();
            foreach($decode as $taskItem){
                
                switch($action){
                    case 'delete':
                        //if we are deleting and item from array
                        if($taskItem->TaskId != $id){
                            array_push($newFile, (array)$taskItem);
                        }
                        break;
                    case 'update':
                        // if we need to update an item
                        if($taskItem->TaskId == $id){
                            $taskItem->TaskName = $this->TaskName;
                            $taskItem->TaskDescription = $this->TaskDescription;
                            array_push($newFile, (array)$taskItem);
                        }else{
                           array_push($newFile, (array)$taskItem);
                        }
                        break;
                    case 'find':
                        //this is if we are looking to find one item
                         if($taskItem->TaskId == $id){
                         // we will break out the loop when we have found it
                            return (array)$taskItem;
                        }
                        break;
                    default :
                        array_push($newFile, (array)$taskItem);
                        break;
                    
                }
            }
            
            //lets add items if we need to
            if($action == 'add'){
                $taskItem = array();
                $taskItem['TaskId']             = $this->TaskId;
                $taskItem['TaskName']           = $this->TaskName;
                $taskItem['TaskDescription']    = $this->TaskDescription;
                array_push($newFile, $taskItem);
            }
            return $newFile;
    }
    
    private function MakeHtml($taskArray){
        //extracted the previous html part to refactor it a bit so we can use it for generating the list each time to make sure requests stay fresh.
        $html = '';
        if (sizeof($taskArray) > 0) {
            
            foreach ($taskArray as $task) {
                $html .= '<a id="'.$task['TaskId'].'" href="#" class="list-group-item" data-toggle="modal" data-target="#myModal">
                            <h4 id="'.$task['TaskId'].'-list-group-item-heading">'.$task['TaskName'].'</h4>
                            <p id="'.$task['TaskId'].'-list-group-item-text">'.$task['TaskDescription'].'</p>
                        </a>';
            }
        }else{
            $html .= '<a id="newTask" href="#" class="list-group-item" data-toggle="modal" data-target="#myModal">
                     <h4 id="0-list-group-item-heading">No Tasks Available</h4>
                     <p id="0-list-group-item-text">Click here to create one</p>
                </a>';
        }
        
        return $html;
    }
    
}