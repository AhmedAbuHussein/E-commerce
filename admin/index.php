<?php 
    session_start();
    $noNavbar = "";
    $pageTitle="Login";

    include "init.php";

    if(isset($_SESSION['admin'])){
        header("Location:dashboard.php");
        exit();
    }

    if($_SERVER['REQUEST_METHOD'] == "POST"){
        $user = $_POST['user'];
        $pass = sha1($_POST['pass']);

        $stmt = $con->prepare("SELECT 
                                    userid,username, password 
                                FROM 
                                    users 
                                WHERE 
                                    username = ? 
                                AND 
                                    password = ? 
                                AND 
                                    groupid = 1
                                LIMIT 1");

        $stmt->execute(array($user,$pass));
        $row = $stmt->fetch();
        $count = $stmt->rowCount(); 
        
        if($count > 0){
            $_SESSION['admin'] = $user;
            $_SESSION['id'] = $row['userid'];
            header('Location:dashboard.php');
            exit();
        }
        
    }


 ?>

    <form class="login" method="POST" action="<?php echo $_SERVER['PHP_SELF'] ?>">
        <h3 class="text-center">Admin Login</h3>
        <input class="form-control" name="user" type="text" placeholder="User name" autocomplete='of'/>
        <input class="form-control" name="pass" type="password" placeholder="Password" autocomplete='new-password'/>
        <input class="btn btn-primary btn-block" type="submit" value="Login" />
    </form>
    


<?php include $templates . "footer.php"; ?>