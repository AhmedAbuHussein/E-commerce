<?php
    ob_start();
    session_start();
    $pageTitle = "login";  
    if(isset($_SESSION['user'])){
        header('location:index.php');
        exit();
    }
    include 'init.php';
    $formError=array();
    if($_SERVER['REQUEST_METHOD'] == 'POST'){

        $_SESSION['activePage'] = $_POST['info'];

        if($_POST['info'] == 'signin'){
            $name = filter_var($_POST['username'],FILTER_SANITIZE_STRING);
            $pass = filter_var($_POST['password'],FILTER_SANITIZE_STRING);
            if(empty($name)){
                $formError[] = 'Invalid User Name!';
            }
            if (empty($pass)) {
                $formError[]= 'Empty Password';
            }
            $pass = sha1($pass);

            if(count($formError) <= 0){

                $chkUser = checkUser($name);
                
                if($chkUser){
                    $stmt = $con->prepare('SELECT userid FROM users WHERE username = ? AND password = ? LIMIT 1');
                    $stmt->execute(array($name,$pass));
                    $chk = $stmt->rowCount();
                    $userid = $stmt->fetch();
                    if($chk > 0){
                        $_SESSION['user'] = $name;
                        $_SESSION['userid'] = $userid['userid'];
                        header('location:index.php');
                        exit();
                    }else{
                        $formError[] = "Invalid Password";
                    }
                }else{
                    $formError[] = "Invalid User Name";
                }
            }

        }elseif($_POST['info'] == 'signup'){

            $username =  filter_var($_POST['username'],FILTER_SANITIZE_STRING);
            $email = filter_var($_POST['email'],FILTER_SANITIZE_EMAIL);
            $pass = filter_var($_POST['password'],FILTER_SANITIZE_STRING);

            if(empty($username)){
                $formError[]= 'User Name Required!';
            }
                

            if(empty($email)){
                $formError[]= 'Email Address Required!';
            }else{

                if(filter_var($email,FILTER_VALIDATE_EMAIL) != true){
                    $formError[] = 'Invalid Email Address';
                }
            }


            if(empty($pass)){
                $formError[]= 'User Password Required!';
            }else{
                $pass = sha1($pass);
            }

            if(count($formError) <= 0){
                
                if(checkUser($username) > 0){
                    $formError[] = "User Name Already Exist!";
                }else{

                    $stmt = $con->prepare("INSERT INTO users (username,password,email,regstatus,date) VALUES (?,?,?,0,now())");
                    $stmt->execute(array($username,$pass,$email));

                    $stmt = $con->prepare("SELECT userid FROM users WHERE username = ? LIMIT 1");
                    $stmt->execute(array($username));
                    $newuser = $stmt->fetch(PDO::FETCH_ASSOC);

                     $_SESSION['user'] = $username;
                     $_SESSION['userid'] = $newuser['userid'];
                    header('location:index.php');
                    exit();

                }

            }

        }
    }
    ?>

<div class="container log-page">
    <h2 class="text-center">
        <span class="<?php
                         if(!isset($_SESSION['activePage'])){
                              echo'selected'; 
                            }else{
                                if($_SESSION['activePage'] == 'signin' ){
                                    echo 'selected';
                                }
                            }
                     ?>" data-class="signin">Sign In</span> | 
        <span class="<?php echo (isset($_SESSION['activePage']) && $_SESSION['activePage'] == 'signup' )? 'selected' :''; ?>" data-class="signup">Sign Up</span>
    </h2>
    <form class="signin" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="POST">
        <input name='info' type='hidden' value='signin'>
        <input class="form-control" value="<?php echo !empty($name)?$name:''; ?>" type="text" name="username" placeholder="User Name" autocomplete="off" />
        <input class="form-control" type="password" name="password" placeholder="User Password" autocomplete="new-password" />
        <button class="btn btn-primary btn-block"  type="submit" ><i class="fa fa-sign-in"></i> Sign In</button>

    </form>

    <form class="signup" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="POST">
        <input name='info' type='hidden' value='signup'>
        <input class="form-control" type="text" value="<?php echo isset($username)?$username:''; ?>" name="username" placeholder="User Name" autocomplete="off" />
        <input class="form-control" type="email" value="<?php echo isset($email)?$email:''; ?>" name="email" placeholder="Your Email" autocomplete="off" />
        <div class="form-group">
            <input class="form-control password" type="password" name="password" placeholder="User Password" autocomplete="new-password" />
            <i class="show-pass fa fa-eye fa-sm"></i>
        </div>

        <button class="btn btn-success btn-block"  type="submit" ><i class="fa fa-sign-in"></i> Sign Up</button>

    </form>
    <?php  
        if(count($formError) > 0){
            foreach($formError as $error){
                echo "<div class='form-alert error-form'>" . $error . "</div>";
            }

        }
    
    ?>
</div>

<?php 
include $templates . "footer.php";
ob_end_flush();
?>