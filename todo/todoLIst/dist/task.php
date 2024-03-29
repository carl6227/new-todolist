<?php
	class tasks
    {
        private $server = "mysql:host=localhost;dbname=to_do_list";
        private $user = "root";
        private $password = "";
        private $options = array(
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        );

        protected $connection;

        //connections
        public function openConnection()
        {
            try {
               
                $this->connection= new PDO(
                    $this->server,
                    $this->user,
                    $this->password,
                    $this->options
                );
               
                return $this->connection;
            } catch (PDOException $error) {
                echo "Erro connection:" . $error->getMessage();
            }
        }
        //defining closeConnection function
        public function closeConnection()
        {
            $this->$connection= null;
        }
        //creating the displayData function and echo it on a formated card 
        public function displayData()
        {
            $connection = $this->openConnection();
            $statement = $connection->prepare("SELECT * FROM tasks WHERE deletedAt is null");
            $statement->execute();
            
            $taskCount = $statement->rowCount();
			while ($row = $statement->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT)) {
					echo '<div class="col-xl-3 col-md-6 undeletedTask">
					<div class="card bg-info text-white mb-4">
					 
						<div class="card-body ml-4">
							
						<p>	'.$row[1].'</p></div>
						<div class="card-footer d-flex align-items-center justify-content-between">   
                            <div class="container-fluid float-right" style="margin-right:-40px;">
                                    <div class="row"> <input type="checkbox" class="form-check-input check" id="exampleCheck1">
                                        <small>Mark as done</small>
                                        <form method="post">
                                            <input type="hidden" name="ID" value="'.$row[0].'">
                                            <input type="hidden" name="tempTask" value="'.$row[1].'">
                                            <button  type="button"  class="btn btn-outline-secondary btn-sm ml-3 text-light updateBtn" ><ion-icon class="ml-1" name="create-outline"></ion-icon></button>
                                            <button type="submit" class="btn btn-outline-secondary btn-sm text-light ml-2	" name="delete"><ion-icon   name="trash-outline"></button>
                                        </form>
                                        <div class="modal fade updateModal"  tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h4 class="modal-title text-secondary" id="exampleModalLabel">Edit Task</h4>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                    <form method="post">
                                                        <input type="hidden" name="oldTask" value="'.$row[0].'">
                                                        <input type="text" name="newTask" placeholder="'.$row[1].'"class="form-control form-control-lg border-info">
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                        <button type="submit" name="saveChanges" class="btn btn-primary">Save changes</button>
                                                    </form>
                                                        </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                            </div>
						</div>
                        </div>
					
				</div>';
				 
			}
        }

        //creating the displayData function and echo it on a formated card 
		public function displayTrash()
        {
            $connection = $this->openConnection();
            $statement = $connection->prepare("SELECT * FROM tasks WHERE deletedAt is not null");
            $statement->execute();
            
            $taskCount = $statement->rowCount();
			while ($row = $statement->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT)) {
					echo '<div class="col-xl-3 col-md-6 deletedTask" style="display:none">
					<div class="card bg-info text-white mb-4">
					 
						<div class="card-body ml-4">
							
						    <p>	'.$row[1].'</p></div>
                            <div class="card-footer d-flex align-items-center justify-content-between">   
                                <div class="container-fluid float-right" style="margin-right:-40px;">
                                
                                        <div class="row"> 
                                        
                                            <form method="post">
                                                <input type="hidden" name="ID" value="'.$row[0].'">
                                                <button type="submit" class=" ml-3 btn btn-outline-secondary btn-sm text-light" name="retrieve"><ion-icon name="reload-outline"></ion-icon></button>
                                                <button type="submit" class=" ml-2 btn btn-outline-secondary btn-sm text-light" name="remove"><ion-icon   name="trash-outline"></button>
                                        
                                        </div>
                                </div>
                            </div>
					    </div>
                         
				</div>';
				 
			}
        }
        //addTask function inserts data to the database
		public function addTask(){
            if(isset($_POST['addTask']) && $_POST['task'] !=""){
                $dateCreated = date('Y-m-d H:i:s');
                 $task=$_POST['task'];
                 $connection =$this->openConnection();
                 $statement=$connection->prepare("INSERT INTO  tasks(todo,createdAt) VALUES (?,?)");
                 $statement->execute([$task,$dateCreated
				 ]);
            }
         }
         //deleteTask function updates the deletedAt in to current date and time.
		 public function deleteTask(){
            if(isset($_POST['delete'])){
                $dateDeleted = date('Y-m-d H:i:s');
                 $ID=$_POST['ID'];
                 $connection =$this->openConnection();
                 $statement=$connection->prepare("UPDATE   tasks SET deletedAt=? WHERE id=$ID");
                 $statement->execute([$dateDeleted]);
            }
         }
         //delele from trash removes permanently the data form the database 
         public function deleteFormTrash(){
            if(isset($_POST['remove'])){
                 $ID=$_POST['ID'];
                 $connection =$this->openConnection();
                 $statement=$connection->prepare("DELETE FROM  tasks  WHERE id=$ID");
                 $statement->execute();
            }
         }
        // retrieveTask updates the deletedAt in to NULL
         public function retrieveTask(){
            if(isset($_POST['retrieve'])){
                 $ID=$_POST['ID'];
                 $connection =$this->openConnection();
                 $statement=$connection->prepare("UPDATE   tasks SET deletedAt=NULL WHERE id=$ID");
                 $statement->execute();
            }
         }

		 
        // editTask update the task 
         public function editTask(){
            if(isset($_POST['saveChanges'])){
              // echo '<script>console.log("'.$_POST["oldTask"].'")</script>';
               
                 $dateUpdate = date('Y-m-d H:i:s');
				 $newTask=$_POST['newTask'];
                 $oldTask= $_POST['oldTask'];
                 $connection =$this->openConnection();
                 $statement=$connection->prepare("UPDATE tasks SET updatedAt=?, todo=? WHERE id='$oldTask'");
                 $statement->execute([$dateUpdate,$newTask]);
            }
         }
	}
       
        
?>



