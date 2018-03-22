<?php
    ob_start();
    session_start();
    $pageTitle="Profile";
    include "init.php";
    $userid = (isset($_GET['id']) && is_numeric($_GET['id'])) ? intval($_GET['id']) : $_SESSION['userid'];

    $stmt = $con->prepare("SELECT * FROM users WHERE userid = ?");
    $stmt->execute(array($userid));
    $chk = $stmt->rowCount();

    if($chk < 1){
        $userid = $_SESSION['userid'];
    }


    $info = getUserItems($userid);
    $userinfo = getUserInfo($userid);
    $userComments = getUserComments($userid);

    include 'profileHeader.php';
?>

<div class="info">
    <div class="container">
        <div class="row">

            <div class="col-md-5 col-lg-4">
               <div class="box">
                    <div class="panel panel-primary">
                        <div class="panel-heading"><i class="fa fa-chevron-down" ></i>  Main Information
                            <?php 
                                if($userid == $_SESSION['userid']){
                                    echo '<a href="edit.php?action=main" class="btn btn-success pull-right"><i class="fa fa-edit"></i> Edit</a>';
                                }
                            ?>
                        
                        </div>
                        <div class="panel-body">
                            <?php 
                                if(!empty($userinfo['username'])){
                                    echo '<h4 class="text-capitalize"><i class="fa fa-unlock-alt"></i> User Name : <span class="text-success">'. $userinfo['username'] .'</span></h4>
                                    <hr />';
                                }
                            
                                if(!empty($userinfo['email'])){

                                    echo ' <h4 class=""><i class="fa fa-envelope-o"></i> Email :  <span class="text-success">'. $userinfo['email'] . '</span></h4>
                                     <hr/>';
                                }

                                if(!empty($userinfo['fullname'])){
                                    echo ' <h4 class=""><i class="fa fa-user"></i> Full Name :  <span class="text-success">' . $userinfo['fullname'] . '</span></h4>
                                     <hr/>';

                                }
                                if(!empty($userinfo['date'])){
                                    echo '<h4 class=""><i class="fa fa-calendar"></i> Join Date :  <span class="text-success">' . $userinfo['date'] . '</span></h4>
                                    <hr/>';
                                }

                            ?>
                            <h4 class=""><i class="fa fa-tags"></i> Trust Rate :  
                                <span class="text-success">
                                    <?php
                                    for ($i=1; $i <= 5; $i++) { 
                                       if($i <=  intval($userinfo['truststatus'])){
                                           echo '<i class="fa fa-star"></i>';
                                       }else{
                                           echo '<i class="fa fa-star-o" ></i>';
                                       }
                                    } 
                                    ?>
                                </span>
                            </h4>


                        </div>
                    </div>
               </div>

               <div class="box">

                    <div class="panel panel-primary">
                        <div class="panel-heading"><i class="fa fa-chevron-down" ></i>  Latest Comments </div>
                        <div class="panel-body">
                            
                            <?php
                                if(count($userComments) >0){
                                    foreach ($userComments as $comment) {
                                        echo '<div class="comment">';
                                            echo '<h4> Item : ' . $comment['name'] . '</h4>';
                                            echo '<p>' . $comment['comment'] . '</p>';
                                        echo '</div>';
                                    }
                                }else{
                                    echo "<p class='text-muted'>You don't Have Any Comments!</p>";
                                }

                            ?>

                        </div>
                    </div>

               </div>


            </div>

           <div class="col-md-7 col-lg-8">
            <?php if(count($info) > 0){ ?>

              <ul class="gallary-menu">
                <?php 

                    $stmt = $con->prepare('SELECT name FROM category');
                    $stmt->execute();
                    $cats = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    echo '<li class="filter btn selected" data-filter="all">All</li>';
                    foreach($cats as $cat){
                        echo '<li class="filter btn" data-filter=".' .  str_replace(" ","-",$cat['name']) . '">' . $cat['name'] . '</li>';
                    }
                ?>
                </ul>
                
                <div id="gallery">

                    <?php 
                        foreach($info as $item){
                            echo '<div class="col-xs-12 col-sm-4 mix ' . str_replace(" ","-",$item['catname']) . '">';
                                echo '<div class="box-gallary">';
                                    echo '<span class="price">' . $item['price'] . '</span>';
                                    echo '<div class="img">';
                                        echo '<img class="img-responsive center-block" src="data:image;base64,' . $item['img'] . '" alt="image"/>';
                                    echo '</div>';
                                    echo '<div class="item-info text-center">';
                                        echo '<h3><a href="show.php?id=' . $item['id'] . '">' . $item['name'] . '</a></h3>';
                                        echo '<p class="text-muted">' . $item['description'] . '</p>';
                                          
                                            if($userid == $_SESSION['userid']){
                                                echo '<div class="edit-box">
                                                        <a href="edit.php?action=data&id=' . $item['id'] . '"><i class="fa fa-edit"></i> Edit</a>
                                                    </div>';    
                                            }
                                    echo '</div>';
                                echo '</div>';
                            echo '</div>';
                        }
                    ?>

                </div>
            <?php }else{
                echo "<div class='text-center'><h3>Ther's No Ads To show!</h3></div>";
            }?>
           </div>

          

        </div>
    </div>
</div>

<?php 
    include $templates . "footer.php"; 
    ob_end_flush();
    ?>