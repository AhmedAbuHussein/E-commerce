

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title><?php printTitle(); ?></title>
        <link rel="stylesheet" href="<?php echo $css ?>bootstrap.min.css" />
        <link rel="stylesheet" href="<?php echo $css ?>jquery-ui.min.css" />
        <link rel="stylesheet" href="<?php echo $css ?>jquery.selectBoxIt.css" />
        <link rel="stylesheet" href="<?php echo $css ?>font-awesome.min.css" />
        <link rel="stylesheet" href="<?php echo $css ?>front.css" />
    </head>
    <body>
       
        <nav class="navbar navbar-inverse">
            <div class="container">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-nav" aria-expanded="false">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="index.php">Home</a>
                </div>
                <div class="collaps navbar-collaps" id="app-nav">
                    <ul class="nav navbar-nav">
                        
                        <?php

                            foreach(getCat() as $cat){
                                echo "<li><a href='categories.php?catid=" . $cat['id'] . "&catname=" . str_replace(" ","-",$cat['name']) . "'> " .$cat['name'] . " </a></li>";
                            }

                        ?>
                    </ul>

                     <?php if(isset($_SESSION['user'])){ 
                            $stmt = $con->prepare("SELECT * FROM users WHERE userid = ?");
                            $stmt->execute(array($_SESSION['userid']));
                            $data = $stmt->fetch(PDO::FETCH_ASSOC);

                           if(checkUser($_SESSION['user'],'AND regstatus = 0')){
                               //user unregisterd
                           }
                       ?>
                        <div class="dropdown navbar-right upper-nav">
                                <button class="btn dropdown-toggle text-capitalize" type="button" data-toggle="dropdown">
                             <?php 
                                
                    echo '<img src="data:image;base64,'. $data['img'] . '" alt="profile"> ' . $_SESSION['user'] ; 

                             ?>  
                                <span class="caret"></span></button>
                                <ul class="dropdown-menu">
                                    <li><a href="profile.php">Profile</a></li>
                                        <?php if($data['groupid'] == 1){ 
                                        $_SESSION['admin'] = $data['username'];
                                        $_SESSION['id'] = $data['userid'];
                                    ?>
                                    <li><a href="admin/dashboard.php">Dashboard</a></li>
                                    <?php } ?>
                                    <li><a href="edit.php?action=main">Edit</a></li>
                                    <li><a href="newads.php">New Ads </a></li>
                                    <li class="divider"></li>
                                    <li><a href="logout.php">Logout</a></li>
                                </ul>
                            </div>
                <?php   }else{
                            echo '<a href="login.php" class="btn pull-right upper-btn">
                                    <span>singup | signin</span>
                                </a>';

                        }?>

                </div>

        </div>
        </nav>
            
