<?php
    ob_start("ob_gzhandler");
    session_start();

if(isset($_SESSION['admin'])){
    $pageTitle = "Members";
    include 'init.php';

    $action = isset($_GET['action']) ? $_GET['action'] : 'manage';

    if($action == 'manage'){ //manage page

        $query = "";
        if(isset($_GET['page']) && $_GET['page'] == 'pending'){
            $query = "AND regstatus = 0";
        }
    
        $stmt = $con->prepare("SELECT * FROM users WHERE groupid != 1 $query");
        $stmt->execute();

        $rows = $stmt->fetchAll();
    
    ?>
        <h2 class="text-center">Manage Members</h2>
       <div class="container">

            <div class="table-responsive">
                <table class="cust-table table  text-center">
                    <tr>

                        <th>#ID</th>
                        <th>User Name</th>
                        <th>Email</th>
                        <th>Full Name</th>
                        <th>Registered Data</th>
                        <th>Control</th>

                    </tr>

                    <?php

                        foreach($rows as $row){
                            echo "<tr>";
                            echo "<td> " . $row['userid'] . "</td>";
                            echo "<td> " . $row['username'] . "</td>";
                            echo "<td> " . $row['email'] . "</td>";
                            echo "<td> " . $row['fullname'] . "</td>";
                            echo "<td> " . $row['date'] . "</td>";

                            echo "<td>
                                    <a href='members.php?action=edit&id=" . $row['userid'] . "' class='btn btn-success'><i class='fa fa-edit'></i> Edit</a>
                                    <a href='members.php?action=delete&id=" . $row['userid'] . "' class='btn btn-danger confirm'><i class='fa fa-close'></i> Del</a>";
                            if($row['regstatus'] == 0){
                                echo " <a href='members.php?action=active&id=" . $row['userid'] . "' class='btn btn-info'><i class='fa fa-check'></i> Active</a>";
                            }
                            echo "</td>";

                            echo "</tr>";
                        }

                    ?>                   
                </table>
            </div>
            <a class="btn btn-primary" href="members.php?action=add"><i class="fa fa-plus"></i> Add New Member</a>
         </div>

<?php }elseif($action == 'add'){ // add new member ?>



        <h2 class="text-center text-capitalize">Add New Member</h2>
        <div class="form-container">
            <form method="POST" class="form-horizontal" action="members.php?action=insert">
                <div class="form-group text-center">
                    <label class="control-label">User Name</label>
                    <input type="text" required="required" name='username' class="form-control" autocomplete='off' placeholder="User Name"/>
                </div>

                <div class="form-group text-center">
                    <label class="control-label">Password</label>
                    <input type="password" required="required" placeholder="User Password"  name='password' class="password form-control" autocomplete='new-password'/>
                    <i class="show-pass fa fa-eye fa-md"></i>
                </div>

                <div class="form-group text-center">
                    <label class="control-label">E-Mail</label>
                    <input type="email" required="required"  name='email' placeholder="Email" class="form-control" autocomplete='off'/>
                </div>

                <div class="form-group text-center">
                    <label class="control-label">Full Name</label>
                    <input type="text" required="required" placeholder="Full Name" name='full' class="form-control" autocomplete='off'/>
                </div>

                <div class="form-group text-center">
                    <input type="submit" value='Add Member' class="btn btn-primary btn-block"/>
                </div>
            </form>
        </div>


   <?php 
   
    }elseif($action == "active"){   // Activate users


        $id = (isset($_GET['id']) && is_numeric($_GET['id'])) ? intval($_GET['id']) : 0;

        $check = checkItem("userid","users",$id);

        if($check > 0){
            $stmt = $con->prepare("UPDATE users SET regstatus = 1  WHERE userid = ? LIMIT 1");
            $stmt->execute(array($id));

            echo '<div class="container">
                    <h2 class="text-center">Activate Member</h2>
                    <div class="alert alert-success">' . $stmt->rowCount() . ' Record Update Successful !</div>';
            
            redirect("<div class='alert alert-success text-center'>You will redirect in 5s</div>","back",5);
            echo '</div>';
        }else{

            echo '<div class="container">
                    <h2 class="text-center">Delete Member</h2>
                    <div class="alert alert-danger"> User not Found !</div>';

            redirect("<div class='alert alert-success text-center'>You will redirect in 5s</div>","back",5);
            echo '</div>';
        }

        

    }elseif($action == "insert"){// insert new member into database

        if($_SERVER['REQUEST_METHOD'] == "POST"){
            echo "<h2 class='text-center'>Add New Member</h2>";
            echo "<div class='container'>";
           
            $user = $_POST['username'];
            $email = $_POST['email'];
            $name = $_POST['full'];
            $pass =  $_POST['password'];

            $hpass = sha1($_POST['password']);

            $formErrors = array();
            if(empty($user)){
                $formErrors[] = "User Name Can't Be  <strong>Empty</strong>!";
            }elseif(strlen($user) < 3){
                $formErrors[] = "User Name Can't Be Smaller Than <strong>3 Characters</strong> !";
            }
            if(empty($email)){
                $formErrors[] = "E-Mail Can't Be <strong>Empty</strong> !";
            }
            if(empty($name)){
                $formErrors[] = "Full Name Can't Be <strong>Empty</strong> !";
            }
            if(empty($pass)){
                $formErrors[] = "Password Can't Be <strong>Empty</strong> !";
            }

            if(count($formErrors) > 0 ){

                foreach($formErrors as $error){
                    echo "<div class='alert alert-danger lead text-center'>" . $error . "</div>";
                }
                $formErrors = array();                    
               redirect("<div class='alert alert-danger text-center'>you will redirect to in 10s</div>","back",5);
                
            }else{

                $count = checkItem("username","users",$user);

                if($count > 0){

                    redirect("<div class='alert alert-danger text-center'>User Name [ " . $user . " ] Already Exist!</div>","manage",3);

                }else{

                    $stmt = $con->prepare("INSERT INTO users(username,password,email,fullname,regstatus,date) VALUES(:zuser,:zpass,:zemail,:zfull, 1 , now())");
                    $stmt->execute(array(
                        'zuser' => $user,
                        'zpass' => $hpass,
                        'zemail'=> $email,
                        'zfull' => $name
                    ));
        
                    redirect("<div class='alert alert-success text-center'>" .$stmt->rowCount() ." Member Added Successful!</div>","manage",3);

                }
            }


        }else{
            redirect("",NULL,0);
        }
        
    }elseif($action == "edit"){ //Edit page
    
        $user = (isset($_GET['id']) && is_numeric($_GET['id'])) ? intval($_GET['id']) : 0;
        
        $stmt = $con->prepare('SELECT * FROM users WHERE userid = ? LIMIT 1');
        $stmt->execute(array($user));
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $count = $stmt->rowCount();

        if($count > 0){ ?>
        
            <h2 class="text-center text-capitalize">Edit Member Info</h2>
            <div class="form-container">
                <form method="POST" class="form-horizontal" action="?action=update">
                    <input type="hidden" name="userid" value="<?php echo $user ?>" />
                    <div class="form-group text-center">
                        <label class="control-label">User Name</label>
                        <input type="text" required="required"  value="<?php echo $row['username'] ?>" name='username' class="form-control" autocomplete='off'/>
                    </div>

                    <input type="hidden" value="<?php echo $row['password'] ?>"  name='oldpassword'/>
                    <div class="form-group text-center">
                        <label class="control-label">Password</label>
                        <input type="password" placeholder="leave Blank.. password not change"  name='password' class="form-control" autocomplete='new-password'/>
                       
                    </div>

                    <div class="form-group text-center">
                        <label class="control-label">E-Mail</label>
                        <input type="email" required="required"  name='email'  value="<?php echo $row['email'] ?>" class="form-control" autocomplete='off'/>
                    </div>

                    <div class="form-group text-center">
                        <label class="control-label">Full Name</label>
                        <input type="text" required="required" value="<?php echo $row['fullname'] ?>" name='full' class="form-control" autocomplete='off'/>
                    </div>

                    <div class="form-group text-center">
                        <input type="submit" value='Save' class="btn btn-primary btn-block"/>
                    </div>
                </form>
            </div>

   <?php
        }else{
            redirect("<div class='alert alert-danger text-center'>Invalid User ID</div>",NULL,3);
        }
   }elseif($action == "update"){

        if($_SERVER['REQUEST_METHOD']=="POST"){

            echo "<h2 class='text-center'>Members Update</h2>";
            echo "<div class='container'>";
            $id = $_POST['userid'];
            $user = $_POST['username'];
            $email = $_POST['email'];
            $name = $_POST['full'];
            $pass = (empty($_POST['password'])) ?  $_POST['oldpassword'] : sha1($_POST['password']);

            $formErrors = array();
            if(empty($user)){
                $formErrors[] = "User Name Can't Be  !";
            }elseif(strlen($user) < 3){
                $formErrors[] = "User Name Can't Be Smaller Than <strong>3 Characters</strong> !";
            }
            if(empty($email)){
                $formErrors[] = "E-Mail Can't Be <strong>Empty</strong> !";
            }
            if(empty($name)){
                $formErrors[] = "Full Name Can't Be <strong>Empty</strong> !";
            }

            if(count($formErrors) > 0 ){

                foreach($formErrors as $error){
                    echo "<div class='alert alert-danger lead text-center'>" . $error . "</div>";
                }
                $formErrors = array();                    
                redirect("<div class='alert alert-danger text-center'> you will redirect in 10s</div>","back",10);
                
            }else{

                $stmt = $con->prepare("UPDATE users SET username = ?, password=? , email=? , fullname = ? WHERE userid = ?");
                $stmt->execute(array($user,$pass,$email,$name,$id));
                redirect("<div class='alert alert-success text-center'>" . $stmt->rowCount() ." Record Was Updated! </div>","manage",3);
            }
 
        }else{
           redirect("",NULL,0);
        }

        echo "</div>";


   }elseif($action == "delete"){

        $id = (isset($_GET['id']) && is_numeric($_GET['id'])) ? intval($_GET['id']) : 0;

        $check = checkItem("userid","users",$id);

        if($check > 0){
            $stmt = $con->prepare("DELETE FROM users WHERE userid = ?");
            $stmt->execute(array($id));

            echo '<div class="container">
                    <h2 class="text-center">Delete Member</h2>
                    <div class="alert alert-success">' . $stmt->rowCount() . ' Record Delete Successful !</div>';
            
            redirect("<div class='alert alert-success text-center'>You will redirect in 5s</div>","back",5);
            echo '</div>';
        }else{

            echo '<div class="container">
                    <h2 class="text-center">Delete Member</h2>
                    <div class="alert alert-danger"> User not Found !</div>';

            redirect("<div class='alert alert-success text-center'>You will redirect in 5s</div>","back",5);
            echo '</div>';
        }
   }



    include $templates . "footer.php";
}else{
   header("location:index.php");
   exit();
}

ob_end_flush();
?>