<?php

    ob_start();

   function printTitle()
   {
        global $pageTitle;
        if(isset($pageTitle)){
            echo $pageTitle;
        }else{
            echo 'Default';
        }
   }

   function checkItem($colName,$tableName,$item,$option=""){

        global $con;
        $stmt = $con->prepare("SELECT $colName FROM $tableName WHERE  $colName = ? $option");
        $stmt->execute(array($item));

        return $stmt->rowCount();
   }


   function redirect($error="" ,$url=NULL, $time=3)
   {


        if($url == NULL){

            $url = "index.php";
        }elseif($url == "manage"){
            $url = "members.php?action=manage";
        }else if($url == "back"){

            if(isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER'] !== ""){
                $url = $_SERVER['HTTP_REFERER'];
            }else {
                $url = "index.php";
            }
        }

        echo $error ;
        header("refresh:$time;url=$url");
        exit();
   }

   function countItems($items,$tableName)
   {
       global $con;
       $stmt = $con->prepare("SELECT COUNT($items) FROM $tableName");
       $stmt->execute();

       return $stmt->fetchColumn();
   }

   function getLatest($selector, $table,$order,$limit = 5){

        global $con;
        $stmt = $con->prepare("SELECT $selector FROM $table ORDER BY $order DESC LIMIT $limit");
        $stmt->execute();

        $rows = $stmt->fetchAll();
        return $rows;
   }

   function ordering()
   {
       header("location:category.php?action=manage&sort=$sort");
       exit();
   }




   ob_end_flush();
   ?>