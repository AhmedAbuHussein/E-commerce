<?php

    ob_start();  //(ob_gzhandler) to solving header is already send problem ( header() )

    session_start();

    if(isset($_SESSION['admin'])){
        $pageTitle = "Dashboard";
        include 'init.php';
        $latestNum = 5;
        $latestusers = getLatest("*","users","userid",$latestNum);
        $latestItems = getLatest('*','items',"id",$latestNum);

        ?>
        <h2 class="text-center dashboard">Administration</h2>
        <div class="home-stat">
            <div class="container text-center">
                <div class="row">

                    <div class="col-md-3">
                        <a href="members.php" class="stat st-members">
                            <i class="fa fa-users"></i>
                            <div class="info">
                                Total Members
                                <span><?php echo countItems("userid","users") ?></span>
                            </div>
                        </a>

                    </div>
                    <div class="col-md-3">
                        <a href="members.php?action=manage&page=pending" class="stat st-pending">
                            <i class="fa fa-user-plus"></i>
                            <div class="info">
                                Pending Members
                                <span>
                                    <?php
                                    echo checkItem("regstatus",'users',0);
                                    ?>
                                </span>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="items.php?action=manage" class="stat st-items">
                            <i class="fa fa-tags"></i>
                            <div class="info">
                                Total Items
                                <span>
                                    <?php
                                    echo countItems("id",'items');
                                    ?>
                                </span>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="comments.php" class="stat st-comments">
                            <i class="fa fa-comments"></i>
                            <div class="info">
                                Total Comments
                                <span><?php echo countItems("id","comments") ?></span>
                            </div>
                        </a>
                    </div>

                </div>
            </div>
        </div>

        <div class="latest">
            <div class="container">
                <!--START ROW-->
                <div class="row">
                    <div class="col-sm-6">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <i class="fa fa-users"></i> Latest <?php echo $latestNum ?> Registered Users
                                <span class="show-panel pull-right">
                                    <i class="fa fa-plus fa-lg"></i>
                                </span>
                            </div>
                            <div class="panel-body">
                                <ul class="list-unstyled latest-list">
                                <?php
                                    foreach($latestusers as $user){
                                        $act = ($user['regstatus'] == 1) ? "": "Not Active";
                                        echo "<li>" . $user['fullname'] . "
                                        <a href='members.php?action=edit&id=" . $user['userid'] . "' class='btn btn-success pull-right'><i class='fa fa-edit'></i> Edit</a>  
                                         <span class='badge pull-right'>$act</span></li> ";
                                    }
                                ?>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <i class="fa fa-tag"></i> Latest <?php echo $latestNum;  ?> Items
                                <span class="show-panel pull-right">
                                    <i class="fa fa-plus fa-lg"></i>
                                </span>
                            </div>
                            <div class="panel-body">
                                <ul class="list-unstyled latest-list">
                                    <?php
                                        foreach($latestItems as $item){
                                            $act = ($item['approve'] == 1) ? "": "Not Approve";
                                            echo "<li>" . $item['name'] . "
                                            <a href='items.php?action=edit&id=" . $item['id'] . "' class='btn btn-success pull-right'><i class='fa fa-edit'></i> Edit</a>  
                                            <span class='badge pull-right'>$act</span></li> ";
                                        }
                                    ?>
                                </ul>
                            </div>
                        </div>
                    </div>

                </div>
                <!--END OF ROW-->
                <!--START ROW-->
                <div class="row">

                    <div class="col-sm-6">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <i class="fa fa-comments"></i> Latest <?php echo $latestNum ?> Comments
                                <span class="show-panel pull-right">
                                    <i class="fa fa-plus fa-lg"></i>
                                </span>
                            </div>
                            <div class="panel-body">
                                <?php

                                        $stmt = $con->prepare("SELECT comments.*, users.username From comments INNER JOIN users ON users.userid = comments.user_id LIMIT $latestNum");
                                        $stmt->execute();
                                        $rows = $stmt->fetchAll();
                                        
                                        foreach($rows as $row){

                                            echo "<div class='row'>";
                                                echo "<div class='col-md-4'><div class='user'><img src='" . $pathImg . "0.jpg' alt='image'/><h3 class='lead'>" . $row['username'] . "</h3></div></div>";
                                                echo "<div class='col-md-8'><div class='comments'>" . $row['comment'] ;

                                                    echo "<div class='controls text-center'>";
                                                    echo "<a href='comments.php?action=edit&id=" . $row['id'] . "' class='btn btn-success'><i class='fa fa-edit'></i> Edit</a>";
                                                    
                                                echo "</div></div></div>";
                                                
                                            echo "</div>";
                                        }
                                    
                                ?>
                            </div>
                        </div>
                    </div>

                </div>
                <!--END OF ROW-->

            </div>
        </div>

        <?php

        include $templates . "footer.php";

    }else{
        header('Location:index.php');
        exit();
    }

    ob_end_flush();
?>