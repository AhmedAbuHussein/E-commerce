<?php
    ob_start();
    session_start();

    if(isset($_SESSION['admin'])){
        $pageTitle = "Items";
        include 'init.php';
        $action = (isset($_GET['action'])) ? $_GET['action'] : 'manage'; 

        if($action == "manage"){
    
            $stmt = $con->prepare("SELECT items.*, category.name AS cat_name, users.username FROM items INNER JOIN category ON category.id = items.cat_id INNER JOIN users ON users.userid = items.user_id");
            $stmt->execute();
    
            $rows = $stmt->fetchAll();
    
        ?>
            

            <h2 class="text-center">Manage Items</h2>
            <div class="container">

            <div class="table-responsive">
                <table class="cust-table table  text-center">
                    <tr>

                        <th>#ID</th>
                        <th>Item Name</th>
                        <th>Description</th>
                        <th>Price</th>
                        <th>Add Date</th>
                        <th>Category</th>
                        <th>Owner</th>
                        <th>Control</th>

                    </tr>

                    <?php

                        foreach($rows as $row){
                            echo "<tr>";
                            echo "<td> " . $row['id'] . "</td>";
                            echo "<td> " . $row['name'] . "</td>";
                            echo "<td> " . $row['description'] . "</td>";
                            echo "<td> " . $row['price'] . "</td>";
                            echo "<td> " . $row['add_date'] . "</td>";
                            echo "<td> " . $row['cat_name'] . "</td>";
                            echo "<td> " . $row['username'] . "</td>";

                            echo "<td>
                                    <a href='items.php?action=edit&id=" . $row['id'] . "' class='btn btn-success'><i class='fa fa-edit'></i> Edit</a>
                                    <a href='items.php?action=delete&id=" . $row['id'] . "' class='btn btn-danger confirm'><i class='fa fa-close'></i> Del</a> ";
                                    if($row['approve'] == 0){
                                        echo " <a href='items.php?action=approve&id=" . $row['id'] . "' class='btn btn-info'><i class='fa fa-check'></i> Approve</a>";
                                    }
                            echo "</td>";

                            echo "</tr>";
                        }
                        ?>
                </table>
            </div>
             <a class="btn btn-primary" href="items.php?action=add"><i class="fa fa-plus"></i> Add item</a>
         </div>
            


<?php   }elseif($action == "add"){?>

            <h2 class="text-center">Add New Item</h2>
            <div class="form-container text-center">

                <form class="form" action="items.php?action=insert" method="POST">

                    <div class="form-group">
                        <label class="control-label">Name</label>
                        <input 
                            type="text" 
                            class="form-control" 
                            placeholder="Item Name" 
                            name="name"
                            required="required" 
                             />
                    </div>

                    <div class="form-group">
                        <label class="control-label">Description</label>
                        <input 
                            type="text" 
                            class="form-control" 
                            placeholder="Item Description" 
                            name="description" 
                            required="required"
                             />
                    </div>
                    <div class="form-group">
                        <label class="control-label">Price</label>
                        <input 
                            type="number" 
                            class="form-control" 
                            placeholder="Item Price" 
                            name="price" 
                            required="required"
                             />
                    </div>
                    <div class="form-group">
                        <label class="control-label">Country</label>
                        <input 
                            type="text" 
                            class="form-control" 
                            placeholder="Country of made" 
                            name="country" 
                            required="required"
                             />
                    </div>

                    <div class="form-group">
                        <label class="control-label">Status</label>
                        <select class="form-control" name="status" >
                            <option value="0">Choose Status</option>
                            <option value="1">New</option>
                            <option value="2">Like New</option>
                            <option value="3">Used</option>
                            <option value="4">Old</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="control-label">User</label>
                        <select class="form-control" name="user" >
                            <option value="0">Choose User</option>
                           <?php
                            $stmt = $con->prepare("SELECT * FROM users");
                            $stmt->execute();
                            $users = $stmt->fetchAll();

                            foreach ($users as $user) {
                                echo "<option value='" . $user['userid'] . "'>" . $user['username'] . "</option>";
                            }

                           ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="control-label">Category</label>
                        <select class="form-control" name="category" >
                            <option value="0">Choose Category</option>
                            <?php
                                $stmt = $con->prepare("SELECT * FROM category");
                                $stmt->execute();
                                $categories = $stmt->fetchAll();

                                foreach ($categories as $cat) {
                                    echo "<option value='" . $cat['id'] . "'>" . $cat['name'] . "</option>";
                                }

                           ?>
                        </select>
                    </div>

                    <div class="form-group">

                        <input type="submit" value="Add Item" class="btn btn-block  btn-primary" />

                    </div>

                   

                </form>
            </div>


<?php   }elseif($action == "insert"){

            $errors = array();

            if($_SERVER['REQUEST_METHOD'] == "POST"){

                echo '<h2 class="text-center">Insert Item </h2>';
                echo '<div class="container">';

                $name    = filter_var($_POST['name'],FITLER_SANITIZE_STRING);
                $des     = $_POST['description'];
                $price   =  filter_var($_POST['price'],FITLER_SANITIZE_NUMBER_INT);
                $country = $_POST['country'];
                $status  = $_POST['status'];
                $user    = $_POST['user'];
                $cat    = $_POST['category'];

                if(empty($name)){
                    $errors[] = "Name Of Itme Must Not Be Empty!";
                }
                if(empty($des)){
                    $errors[] = "Description Of Item Must Not Be Empty!";
                }if(empty($price)){
                    $errors[] = "Price Of Item Must Not Be Empty!";
                }else{
                    if($price < 0){
                        $errors[] = 'Invalid Price';
                    }
                }
                if(empty($country)){
                    $errors[] = "Country Of Made Must Not Be Empty!";
                }
                if($status == 0){
                    $errors[] = "You Must Choose The Item Status!";
                }
                if($user == 0){
                    $errors[] = "You Must Choose Owner Of The Item!";
                }
                if($cat == 0){
                    $errors[] = "You Must Choose The Category Of The Item!";
                }

                if(count($errors) > 0){
                    foreach ($errors as $error) {
                        echo "<div class='alert alert-danger text-center'>" . $error .  "</div>";
                    }
                    redirect("<div class='alert alert-success text-center'>You will redirect in 5s</div","back",5);
                }else{
                    $stmt = $con->prepare("INSERT INTO items(name,description,price,country_made,status,user_id,cat_id,add_date) 
                                           VALUES (?,?,?,?,?,?,?,now())");
                    $stmt->execute(array($name,$des,$price,$country,$status,$user,$cat));

                    redirect("<div class='alert alert-success text-center'>" . $stmt->rowCount() . " Recored Inserted Successful !</div>","items.php?action=manage");
                }

                echo "</div>";

            }else{
                redirect("",NULL,0);
            }

        }elseif($action == "edit"){


            echo "<div class='container'>";
            $itemId = (isset($_GET['id']) && is_numeric($_GET['id'])) ? intval($_GET['id']) : 0;
            if($itemId > 0){

                $count = checkItem("id","items",$itemId);

                if($count > 0){ 
                    $stmt = $con->prepare("SELECT * FROM items WHERE id = ? LIMIT 1");
                    $stmt->execute(array($itemId));
                    $res = $stmt->fetch(PDO::FETCH_ASSOC);
                ?>

                <h2 class="text-center">Edit Item</h2>
                <div class="form-container text-center">

                    <form class="form" action="items.php?action=update&id=<?php echo $res['id']; ?>" method="POST">
                        <input type="hidden" value="<?php echo $res['id']; ?>" name="id"/>
                        <div class="form-group">
                            <label class="control-label">Name</label>
                            <input 
                                type="text" 
                                class="form-control" 
                                placeholder="Item Name" 
                                name="name"
                                value="<?php echo $res['name'] ?>"
                                required="required" 
                                />
                        </div>

                        <div class="form-group">
                            <label class="control-label">Description</label>
                            <input 
                                type="text" 
                                class="form-control" 
                                placeholder="Item Description" 
                                name="description" 
                                value="<?php echo $res['description'] ?>"
                                required="required"
                                />
                        </div>
                        <div class="form-group">
                            <label class="control-label">Price</label>
                            <input 
                                type="number" 
                                class="form-control" 
                                placeholder="Item Price" 
                                name="price" 
                                value="<?php echo $res['price'] ?>"
                                required="required"
                                />
                        </div>
                        <div class="form-group">
                            <label class="control-label">Country</label>
                            <input 
                                type="text" 
                                class="form-control" 
                                placeholder="Country of made" 
                                name="country" 
                                value="<?php echo $res['country_made'] ?>"
                                required="required"
                                />
                        </div>

                        <div class="form-group">
                            <label class="control-label">Status</label>
                            <select class="form-control" name="status" >
                                <option value="1" <?php if($res['status'] == 1) echo "selected"; ?>>New</option>
                                <option value="2" <?php if($res['status'] == 2) echo "selected"; ?>>Like New</option>
                                <option value="3" <?php if($res['status'] == 3) echo "selected"; ?>>Used</option>
                                <option value="4" <?php if($res['status'] == 4) echo "selected"; ?>>Old</option>
                            </select>
                        </div>

                        <div class="form-group"> 
                            <label class="control-label">User</label>
                            <select class="form-control" name="user" >
                            <?php
                                $stmt = $con->prepare("SELECT * FROM users");
                                $stmt->execute();
                                $users = $stmt->fetchAll();

                                foreach ($users as $user) {
                                    if($res['user_id'] == $user['userid']){
                                        echo "<option value='" . $user['userid'] . "' selected>" . $user['username'] . "</option>";
                                    }else{
                                        echo "<option value='" . $user['userid'] . "'>" . $user['username'] . "</option>";
                                    }
                                    
                                }

                            ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="control-label">Category</label>
                            <select class="form-control" name="category" >
                                <?php
                                    $stmt = $con->prepare("SELECT * FROM category");
                                    $stmt->execute();
                                    $categories = $stmt->fetchAll();

                                    foreach ($categories as $cat) {
                                        if($res['cat_id'] == $cat['id'])
                                        {
                                            echo "<option value='" . $cat['id'] . "' selected>" . $cat['name'] . "</option>";   
                                        }else{

                                            echo "<option value='" . $cat['id'] . "'>" . $cat['name'] . "</option>";
                                        }
                                    }

                            ?>
                            </select>
                        </div>

                        <div class="form-group">

                            <label for="check">Approve Ads</label>
                            <input id="check" type="checkbox" value="1" <?php if($res['approve'] == 1){ echo "checked" ;} ?>  name='approve' />

                        </div>

                        <div class="form-group">

                            <input type="submit" value="Edit" class="btn btn-block  btn-primary" />

                        </div>

                    

                    </form>
                </div>



        <?php   }else{

                    echo "<div class='alert alert-danger text-center'>Invalid Item Id !</div>";
                    redirect("<div class='alert alert-success text-center'>redirect in 5s !</div>",'back',5);

                }
            }else{
                echo "<div class='alert alert-danger text-center'>Invalid Item Id !</div>";
                redirect("<div class='alert alert-success text-center'>redirect in 5s !</div>",'back',5);
            }

            echo "</div>";

        }elseif($action=="update"){

            if($_SERVER['REQUEST_METHOD'] == "POST"){
                $errors = array();
                echo '<h2 class="text-center">Update Item </h2>';
                echo '<div class="container">';
                $id      = $_POST['id'];
                $name    = $_POST['name'];
                $des     = $_POST['description'];
                $price   = $_POST['price'];
                $country = $_POST['country'];
                $status  = $_POST['status'];
                $user    = $_POST['user'];
                $cat    = $_POST['category'];
                if(isset($_POST['approve'])){
                     $appr   = $_POST['approve'];
                 }else{
                    $appr = 0;
                 }
               

                if(empty($name)){
                    $errors[] = "Name Of Itme Must Not Be Empty!";
                }
                if(empty($des)){
                    $errors[] = "Description Of Item Must Not Be Empty!";
                }if(empty($price)){
                    $errors[] = "Price Of Item Must Not Be Empty!";
                }if(empty($country)){
                    $errors[] = "Country Of Made Must Not Be Empty!";
                }
                if($status == 0){
                    $errors[] = "You Must Choose The Item Status!";
                }
                if($user == 0){
                    $errors[] = "You Must Choose Owner Of The Item!";
                }
                if($cat == 0){
                    $errors[] = "You Must Choose The Category Of The Item!";
                }

                if(count($errors) > 0){
                    foreach ($errors as $error) {
                        echo "<div class='alert alert-danger text-center'>" . $error .  "</div>";
                    }
                    redirect("<div class='alert alert-success text-center'>You will redirect in 5s</div","back",5);
                }else{
                    $stmt = $con->prepare("UPDATE items SET name = ?,description = ?,price=?,country_made=?,status=?,user_id=?,cat_id=?, approve = ? WHERE id=?");
                    $stmt->execute(array($name,$des,$price,$country,$status,$user,$cat,$appr,$id));

                    redirect("<div class='alert alert-success text-center'>" . $stmt->rowCount() . " Recored Update Successful !</div>","items.php?action=manage");
                }

                echo "</div>";


            }else{
                redirect("",NULL,0);            }

        }elseif($action == "delete"){

            $itemid = (isset($_GET['id']) && is_numeric($_GET['id'])) ? intval($_GET['id']) : 0;

            if($itemid > 0){
                echo '<h2 class="text-center">Delete Item</h2><div calss="container">';
                $chk = checkItem('id','items',$itemid);
                if($chk){

                    $stmt = $con->prepare("DELETE FROM items WHERE id = ?");
                    $stmt->execute(array($itemid));

                    redirect("<div class='alert alert-success text-center'>" . $stmt->rowCount() . " Recored Delete Successful! </div>",'items.php',5);
                }else{
                    echo "<div class='alert alert-danger text-center'>Invalid Item ID!</div>";
                    redirect("<div class='alert alert-success text-center'>redirect in 3s</div>",'back');
                }
                echo "</div>";
            }else{
                redirect("",NULL,0);
            }


        }elseif($action == "approve"){

            echo "<h2 class='text-center'>Approve Item</h2> <div class='container'>";
            $itemid = (isset($_GET['id']) && is_numeric($_GET['id'])) ? intval($_GET['id']) : 0;
            
            if($itemid > 0){

                $chk = checkItem('id','items',$itemid);
                if($chk){
                    $stmt = $con->prepare("UPDATE items SET approve = 1 WHERE id=?");
                    $stmt->execute(array($itemid));

                    echo "<div class='alert alert-success text-center'>" . $stmt->rowCount() . " Record Approve Successful!</div>";
                    redirect("<div class='alert alert-success text-center'>redirect in 3s</div>",'items.php');

                }else{
                    redirect("<div class='alert alert-danger text-center'>Invalid Item ID!</div>",'items.php',5);
                }

            }else{
                redirect("",NULL,0);
            }

            echo "</div>";
        }


        include $templates . "footer.php";
    }else{
        header("location:index.php");
        exit();
    }

    ob_end_flush();
?>