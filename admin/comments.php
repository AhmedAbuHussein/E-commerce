<?php
    ob_start("ob_gzhandler");
    session_start();

if(isset($_SESSION['admin'])){

    $pageTitle = "Comments";
    include 'init.php';

    $action = isset($_GET['action']) ? $_GET['action'] : 'manage';

    if($action == 'manage'){ //manage page

    
        $stmt = $con->prepare("SELECT comments.*, users.username,items.name AS item_name 
                               FROM comments 
                               INNER JOIN users ON users.userid = comments.user_id 
                               INNER JOIN items ON items.id = comments.item_id ");
        $stmt->execute();
        $rows = $stmt->fetchAll();
    
    ?>
        <h2 class="text-center">Manage Comments</h2>
       <div class="container">

            <div class="table-responsive">
                <table class="cust-table table  text-center">
                    <tr>

                        <th>#ID</th>
                        <th>Comments</th>
                        <th>Item Name</th>
                        <th>User Name</th>
                        <th>Data</th>
                        <th>Control</th>

                    </tr>

                    <?php

                        foreach($rows as $row){
                            echo "<tr>";
                            echo "<td> " . $row['id'] . "</td>";
                            echo "<td style='width:550px'> " . $row['comment'] . "</td>";
                            echo "<td> " . $row['item_name'] . "</td>";
                            echo "<td> " . $row['username'] . "</td>";
                            echo "<td> " . $row['date'] . "</td>";

                            echo "<td>
                                    <a href='comments.php?action=edit&id=" . $row['id'] . "' class='btn btn-success'><i class='fa fa-edit'></i> Edit</a>
                                    <a href='comments.php?action=delete&id=" . $row['id'] . "' class='btn btn-danger confirm'><i class='fa fa-close'></i> Del</a>";
                            if($row['status'] == 0){
                                echo " <a href='comments.php?action=approve&id=" . $row['id'] . "' class='btn btn-info'><i class='fa fa-check'></i> Approve</a>";
                            }
                            echo "</td>";

                            echo "</tr>";
                        }

                    ?>                   
                </table>
            </div>
         </div>

<?php }elseif($action == "edit"){ //Edit page
    
        $comment = (isset($_GET['id']) && is_numeric($_GET['id'])) ? intval($_GET['id']) : 0;
        
        $stmt = $con->prepare('SELECT * FROM comments WHERE id = ? LIMIT 1');
        $stmt->execute(array($comment));
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $count = $stmt->rowCount();

        if($count > 0){ ?>
        
            <h2 class="text-center text-capitalize">Edit Comments</h2>
            <div class="form-container">
                <form method="POST" class="form-horizontal" action="?action=update">
                    <input type="hidden" name="userid" value="<?php echo $comment ?>" />

                    <div class="form-group text-center">
                        <label class="control-label text-">Comment</label>
                        <textarea rows=8 class="form-control" name="comment" ><?php echo $row['comment']; ?> </textarea>
                    </div>

                   
                    <div class="form-group text-center">
                        <input type="submit" value='Save' class="btn btn-primary btn-block"/>
                    </div>
                </form>
            </div>

   <?php
        }else{
            redirect("<div class='alert alert-danger text-center'>Invalid Comment ID</div>",NULL,3);
        }
   }elseif($action == "update"){

        if($_SERVER['REQUEST_METHOD']=="POST"){

            echo "<h2 class='text-center'>Members Update</h2>";
            echo "<div class='container'>";
            $id = $_POST['userid'];
            $com = $_POST['comment'];

            $formErrors = array();
            if(empty($com)){
                $formErrors[] = "must add comment or delet it !";
            }

            if(count($formErrors) > 0 ){

                foreach($formErrors as $error){
                    echo "<div class='alert alert-danger lead text-center'>" . $error . "</div>";
                }
                $formErrors = array();                    
                redirect("<div class='alert alert-danger text-center'> you will redirect in 3s</div>","back",3);
                
            }else{

                $stmt = $con->prepare("UPDATE comments SET comment = ? WHERE id = ?");
                $stmt->execute(array($com,$id));
                redirect("<div class='alert alert-success text-center'>" . $stmt->rowCount() ." Record  Updated! </div>","comments.php",3);
            }
 
        }else{
           redirect("",NULL,0);
        }

        echo "</div>";


   }elseif($action == "delete"){

        $id = (isset($_GET['id']) && is_numeric($_GET['id'])) ? intval($_GET['id']) : 0;

        $check = checkItem("id","comments",$id);

        if($check > 0){
            $stmt = $con->prepare("DELETE FROM comments WHERE id = ?");
            $stmt->execute(array($id));

            echo '<div class="container">
                    <h2 class="text-center">Delete Comment</h2>
                    <div class="alert alert-success">' . $stmt->rowCount() . ' Record Delete Successful !</div>';
            
            redirect("<div class='alert alert-success text-center'>You will redirect in 5s</div>","back",5);
            echo '</div>';
        }else{

            echo '<div class="container">
                    <h2 class="text-center">Delete Comments</h2>
                    <div class="alert alert-danger"> Comment Not Found !</div>';

            redirect("<div class='alert alert-success text-center'>You will redirect in 5s</div>","back",5);
            echo '</div>';
        }
   }elseif($action == 'approve'){
        $id = (isset($_GET['id']) && is_numeric($_GET['id'])) ? intval($_GET['id']) : 0;

        $check = checkItem("id","comments",$id);

        if($check > 0){
            echo "<h2 class='text-center'>Approve Comment</h2><div class='container'>";
            $stmt = $con->prepare("UPDATE comments SET status = 1 WHERE id = ?");
            $stmt->execute(array($id));
            redirect("<div class='alert alert-success text-center'>" . $stmt->rowCount() ." Record  Updated! </div>","comments.php",3);
            echo "</div>";
        }else{
            redirect("",NULL,0);
        }
   }

    include $templates . "footer.php";
}else{
    header("location:index.php");
    exit();
}

ob_end_flush();
?>