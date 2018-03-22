<?php
    ob_start();
    session_start();
    if(isset($_SESSION['admin'])){
        $pageTitle = "Category";
        include 'init.php';
        $action = (isset($_GET['action'])) ? $_GET['action'] : 'manage'; 

        if($action == "manage"){

        }elseif($action == "add"){

        }elseif($action == "insert"){

        }elseif($action == "edit"){

        }elseif($action=="update"){

        }elseif($action == "delete"){

        }elseif($action == "approve"){
            
        }


        include $templates . "footer.php";
    }else{
        header("location:index.php");
        exit();
    }

    ob_end_flush();
?>