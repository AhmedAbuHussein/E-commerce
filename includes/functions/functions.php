<?php

    ob_start();

    function getCat()
    {
        global $con;
        $stmt = $con->prepare("SELECT * FROM category");
        $stmt->execute();
        $res = $stmt->fetchAll();
        return $res;
    }

    function getItems($catID="",$approve=1)
    {
      global $con;
      if(empty($catID)){
        $stmt = $con->prepare("SELECT * FROM items WHERE approve=? ORDER BY id DESC");
        $stmt->execute(array($approve));
      }else{
        $stmt = $con->prepare("SELECT * FROM items WHERE cat_id=? AND approve = ? ORDER BY id DESC");
        $stmt->execute(array($catID,$approve));
      }

        $res = $stmt->fetchAll();
        return $res;
    }


   function printTitle()
   {
        global $pageTitle;
        if(isset($pageTitle)){
            echo $pageTitle;
        }else{
            echo 'Default';
        }
   }

   function checkUser($username,$option=""){
      /*RETURN count */

        global $con;
        $stmt = $con->prepare("SELECT username FROM users WHERE  username = ? $option");
        $stmt->execute(array($username));

        return $stmt->rowCount();
   }

   function getUserItems($userid,$approve=1){
    /*Return user items*/
    global $con;
    $stmt = $con->prepare("SELECT items.*, users.fullname, category.name AS catname FROM items INNER JOIN users ON users.userid = items.user_id INNER JOIN category ON category.id = items.cat_id WHERE users.userid = ? AND items.approve = ?");
    $stmt->execute(array($userid,$approve));

    return $stmt->fetchAll();
   }

   function getUserInfo($userid) {
      /*return user information*/
        global $con;
        $stmt = $con->prepare("SELECT * FROM users  WHERE userid = ? LIMIT 1");
        $stmt->execute(array($userid));

        return $stmt->fetch(PDO::FETCH_ASSOC);
       
   }

   function getItemInfo($itemId){
      global $con;
      $stmt = $con->prepare('SELECT items.* ,users.username,category.id AS catId, category.name AS catName FROM items

                            INNER JOIN users ON users.userid = items.user_id

                            INNER JOIN category ON category.id = items.cat_id

                            WHERE items.id = ? LIMIT 1');

      $stmt->execute(array($itemId));
      $res = $stmt->fetch();
      return $res;
   }


   function getUserComments($user){

        global $con;
        $stmt = $con->prepare("SELECT comments.comment , items.name, users.img FROM comments 

                              INNER JOIN users ON users.userid = comments.user_id 

                              INNER JOIN items ON items.id = comments.item_id

                              WHERE users.userid = ? ORDER BY comments.date DESC LIMIT 5");


        $stmt->execute(array($user));

        $rows = $stmt->fetchAll();
        return $rows;
   }

   function getUsersComments($itemId,$status=0){
      global $con;
      $stmt = $con->prepare("SELECT comments.*,users.img FROM comments
                            INNER JOIN users ON users.userid = comments.user_id
                            WHERE item_id=? AND status=?  ORDER BY id DESC");
      $stmt->execute(array($itemId,$status));
      $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
      return $res;

   }



/********************************************************************************************/

   function countItems($items,$tableName)
   {
       global $con;
       $stmt = $con->prepare("SELECT COUNT($items) FROM $tableName");
       $stmt->execute();

       return $stmt->fetchColumn();
   }

   ob_end_flush();
   ?>