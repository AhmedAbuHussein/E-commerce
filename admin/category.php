<?php
    ob_start();

    session_start();
    if(isset($_SESSION['admin'])){

        $pageTitle = "Category";
        include 'init.php';
        $action = (isset($_GET['action'])) ? $_GET['action'] : 'manage';

        if($action == "manage"){

            $sort = "ASC";
            $order = array("ASC","DESC");
            if(isset($_GET['sort']) && in_array($_GET['sort'],$order)){
                $sort = $_GET['sort'];
            }

            $stmt = $con->prepare("SELECT * FROM category ORDER BY ordering $sort");
            $stmt->execute();
            $cats = $stmt->fetchAll(); ?>
        

            <h2 class="text-center">Manage Categories</h2>
            <div class="container">
                <div class="panel panel-default">
                    <div class="panel-heading">
                       Manage Categories
                        <div class="sorting pull-right">
                            Sorting <a class="<?php if($sort == "ASC"){ echo "active";} ?>" href="category.php?action=manage&sort=ASC">ASC</a> | 
                            <a class="<?php if($sort == "DESC"){ echo "active";} ?>" href="category.php?action=manage&sort=DESC">DESC</a>
                        </div>
                    </div>
                    <div class="panel-body">

                        <?php
                            foreach($cats as $cat){
                                echo "<div class='cat-box'>";
                                    echo "
                                    <a class='btn btn-success btn-sm pull-right' href='category.php?action=edit&id=" . $cat['id'] . "'><i class='fa fa-edit'></i> Edit</a>
                                    <a class='btn btn-danger btn-sm pull-right confirm' href='category.php?action=delete&id=" . $cat['id'] . "'><i class='fa fa-close'></i> Del</a>
                                    ";
                                    echo '<h3 class="cat-header"><i class="fa fa-caret-right fa-sm text-info"></i> ' . $cat['name'] . '</h3>';
                                   
                                    echo '<div class="cat-info">';
                                        echo (empty($cat['description'])) ? '<p>This category dosn\'t have Description </p>' : '<p>' . $cat['description'] . '</p>';
                                        echo ($cat['visibility']==1)? "<span class='badge visibility'>Hidden</span>" : "";
                                        echo ($cat['allow_comment']==1)? "<span class='badge comment'>Comment Disable</span>" : "";
                                        echo ($cat['allow_ads']==1)? "<span class='badge ads'>Ads Disable</span>" : "";
                                    echo '</div>';
                                   

                                echo "</div>";

                            }
                        ?>

                    </div>
                </div>
                <div class="text-left">
                    <a href="category.php?action=add" class="btn btn-primary"><i class="fa fa-plus"></i> Add Category</a>
                </div>
            </div>
<?php

        }elseif($action == "add"){?>
            <h2 class="text-center">Add New Category</h2>
            <div class="form-container text-center">
                <form class="form" action="category.php?action=insert" method="POST">


                    <div class="form-group">
                        <label class="control-label">Name</label>
                        <input type="text" class="form-control" placeholder="Category Name" name="name" required="required" autocomplete="off"/>
                    </div>

                    <div class="form-group">
                        <label class="control-label">Description</label>
                        <input type="text" class="form-control" placeholder="Description of Category" name="description"/>
                    </div>

                    <div class="form-group">
                        <label class="control-label">Ordering</label>
                        <input type="text" class="form-control" placeholder="Number To Order Categorye" name="order" autocomplete="off"/>
                    </div>

                    <div class="row">

                        <div class="col-md-4 radio-group">
                            <div class="box">
                                <label>Visiility</label>
                                
                                <input type="radio" value=0 name="visibility" id="vis-yes" checked/> 
                                <label for="vis-yes">Yes</label>

                                <input type="radio" value=1 name="visibility" id="vis-no"/> 
                                <label for="vis-no">No</label>
                            </div>
                        </div>

                        <div class="col-md-4 radio-group">
                            <div class="box">
                                <label>Allow Commenting</label>
                                
                                <input type="radio" value=0 name="comment" id="com-yes" checked/> 
                                <label for="com-yes">Yes</label>

                                <input type="radio" value=1 name="comment" id="com-no"/> 
                                <label for="com-no">No</label>
                            </div>
                        </div>

                        <div class="col-md-4 radio-group">
                            <div class="box">
                                <label>Allow Ads</label>
                                
                                <input type="radio" value=0 name="ads" id="ads-yes" checked/> 
                                <label for="ads-yes">Yes</label>

                                <input type="radio" value=1 name="ads" id="ads-no"/> 
                                <label for="ads-no">No</label>
                            </div>
                        </div>

                    </div>


                    <div class="form-group">

                        <input type="submit" value="Add Category" class="btn btn-block  btn-primary" />

                     </div>

                </form>
            </div>

<?php   }elseif($action == "insert"){

            if($_SERVER['REQUEST_METHOD'] == "POST"){
                $errors = array();

                $catName = $_POST['name'];
                $descrip = $_POST['description'];
                $order   = $_POST['order'];
                $vis     = $_POST['visibility'];
                $comm    = $_POST['comment'];
                $ads    = $_POST['ads'];

                if(empty($catName)){
                    $errors [] = "Category name are Required !";
                }else{
                    $chk = checkItem("name","category",$catName);
                    if($chk > 0){
                        $errors [] = "Category name Already Exist !";
                    }
                }
                if(!empty($order) && !is_numeric($order)){
                    $errors [] = "Order Must Be A Numerical Number !";
                }

                echo '<div class="container">';
                echo '<h2 class="text-center">Insert New Category</h2>';
                if(count($errors) > 0){
                    
                    foreach($errors as $error){
                        echo '<div class="alert alert-danger text-center">' . $error . '</div>';
                    }
                    $err = "<div class='alert alert-danger text-center'>You Will redirected in 5s</div>";
                    redirect($err,'back',5);
                }else{

                    $stmt = $con->prepare("INSERT INTO category (name,description,ordering,visibility,allow_comment,allow_ads) VALUES(?,?,?,?,?,?)");
                    $stmt->execute(array($catName,$descrip,$order,$vis,$comm,$ads));
                    $msg="<div class='alert alert-success text-center'>" . $stmt->rowCount() . "Record Saves Successful !</div>";
                    redirect($msg,"back",3);

                }
                echo '</div>';

            }else{
                redirect("",NULL,0);
                exit();
            }

       }elseif($action == "edit"){

            $id = $_GET['id'];
            if(isset($id) && is_numeric($id)){
                $chk = checkItem("id","category",$id);
                if($chk > 0){

                    $stmt = $con->prepare("SELECT * FROM category WHERE id = ? LIMIT 1");
                    $stmt->execute(array($id));
                    $res = $stmt->fetch(PDO::FETCH_ASSOC);

        ?>

        <h2 class="text-center text-center">Edit Category Info</h2>
        <div class="form-container text-center">
                <form class="form" action="category.php?action=update" method="POST">

                    <input type="hidden" value="<?php echo $res['id'] ?>" name="id" />
                    <div class="form-group">
                        <label class="control-label">Name</label>
                        <input type="text" class="form-control" value="<?php echo $res['name']?>"  name="name" required="required" autocomplete="off"/>
                    </div>

                    <div class="form-group">
                        <label class="control-label">Description</label>
                        <textarea row = "3" class="form-control" name="description"><?php echo $res['description']; ?></textarea>
                    </div>

                    <div class="form-group">
                        <label class="control-label">Ordering</label>
                        <input type="text" class="form-control" value="<?php echo $res['ordering']?>" name="order" autocomplete="off"/>
                    </div>

                    <div class="row">

                        <div class="col-md-4 radio-group">
                            <div class="box">
                                <label>Visiility</label>
                                
                                <input type="radio" value=0 name="visibility" id="vis-yes" <?php echo ($res['visibility'] == 0)? "checked" :  ""; ?>/> 
                                <label for="vis-yes">Yes</label>

                                <input type="radio" value=1 name="visibility" id="vis-no" <?php echo ($res['visibility'] == 1)? "checked" :  ""; ?>/> 
                                <label for="vis-no">No</label>
                            </div>
                        </div>

                        <div class="col-md-4 radio-group">
                            <div class="box">
                                <label>Allow Commenting</label>
                                
                                <input type="radio" value=0 name="comment" id="com-yes" <?php echo ($res['allow_comment'] == 0)? "checked" :  ""; ?>/> 
                                <label for="com-yes">Yes</label>

                                <input type="radio" value=1 name="comment" id="com-no" <?php echo ($res['allow_comment'] == 1)? "checked" :  ""; ?>/> 
                                <label for="com-no">No</label>
                            </div>
                        </div>

                        <div class="col-md-4 radio-group">
                            <div class="box">
                                <label>Allow Ads</label>
                                
                                <input type="radio" value=0 name="ads" id="ads-yes" <?php echo ($res['allow_ads'] == 0)? "checked" :  ""; ?>/> 
                                <label for="ads-yes">Yes</label>

                                <input type="radio" value=1 name="ads" id="ads-no" <?php echo ($res['allow_ads'] == 1)? "checked" :  ""; ?>/> 
                                <label for="ads-no">No</label>
                            </div>
                        </div>

                    </div>


                    <div class="form-group">

                        <input type="submit" value="Add Category" class="btn btn-block  btn-primary" />

                     </div>

                </form>
            </div>


<?php       
                }else{
                    echo "<div class='container'><h2 class='text-center'>Edit Categories Info</h2>";
                        redirect("<div class='alert alert-danger text-center'>Invalid Category Id ! You will redirect in 5s</div>","back",5);
                    echo "</div>";
                }
            }else{
                redirect("",NULL,0);
            }

        }elseif($action == "update"){

            if($_SERVER['REQUEST_METHOD'] == "POST"){

                $errors = array();
                $id      = $_POST['id'];
                $catName = $_POST['name'];
                $descrip = $_POST['description'];
                $order   = $_POST['order'];
                $vis     = $_POST['visibility'];
                $comm    = $_POST['comment'];
                $ads    = $_POST['ads'];


                if(empty($catName)){
                    $errors [] = "Category name are Required !";
                }else{

                    $opt = " AND  id != " . $id;
                    $chk = checkItem("name","category",$catName,$opt);

                    if($chk > 0){
                        $errors [] = "Category name Already Exist !";
                    }
                }
                if(!empty($order) && !is_numeric($order)){
                    $errors [] = "Order Must Be A Numerical Number !";
                }
                if(empty($descrip)){
                    $descrip = "there is no description for this category";
                }

                echo '<div class="container">';
                echo '<h2 class="text-center">Update Category</h2>';
                if(count($errors) > 0){
                    
                    foreach($errors as $error){
                        echo '<div class="alert alert-danger text-center">' . $error . '</div>';
                    }
                    $err = "<div class='alert alert-danger text-center'>You Will redirected in 5s</div>";
                    redirect($err,'back',5);
                }else{

                    $stmt = $con->prepare("UPDATE category SET name = ?, description = ?,ordering = ? ,visibility = ? ,allow_comment = ?,allow_ads = ? WHERE id = ?");
                    $stmt->execute(array($catName,$descrip,$order,$vis,$comm,$ads,$id));
                    $msg="<div class='alert alert-success text-center'>" . $stmt->rowCount() . "Record Updated Successful !</div>";
                    redirect($msg,"category.php?action=manage",3);

                }
                echo '</div>';

                
            }else{
                redirect("",NULL,0);
            }


        }elseif($action == "delete"){

            echo '<div class="container text-center">';
            if(isset($_GET['id']) && is_numeric($_GET['id'])){

                $id = intval($_GET['id']);
                $chk = checkItem("id","category",$id);
                if($chk){

                    $stmt = $con->prepare("DELETE FROM category WHERE id = ?");
                    $stmt->execute(array($id));
                    
                    redirect('<div class="alert alert-success">' . $stmt->rowCount() . ' Record Deleted Successful !</div>',"back");


                }else{
                    redirect('<div class="alert alert-danger">Invalid Categorical ID !</div>',"back");
                }

            }else{
                redirect('<div class="alert alert-danger">Invalid Categorical ID !</div>');
            }
            echo '</div>';
             
      } // end delete section




        include $templates . "footer.php";

    }else{
        header("location:index.php");
        exit();
    }

    ob_end_flush();

?>