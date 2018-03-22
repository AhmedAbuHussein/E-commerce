<?php
	ob_start();
	session_start();
	$pageTitle = "Edit";
	include 'init.php';
	if(isset($_SESSION['user'])){

		$action = $_GET['action'];

		if($action == 'main'){
			$userinfo = getUserInfo($_SESSION['userid']);
			$formError = array();
			$success = "";
			if($_SERVER['REQUEST_METHOD'] == "POST"){

				$username = filter_var($_POST['username'],FILTER_SANITIZE_STRING);
				$fullname = filter_var($_POST['fullname'],FILTER_SANITIZE_STRING);
				$email 	  =	filter_var($_POST['email'],FILTER_SANITIZE_EMAIL);
				$password = filter_var($_POST['password'],FILTER_SANITIZE_STRING);
				$oldpass  =	filter_var($_POST['odlpassword'],FILTER_SANITIZE_STRING);

				$chk = getimagesize($_FILES['file']['tmp_name']);
		        usleep(200000);
		        if($chk== FALSE){
		            $formError[] = "please select an image !";
		        }else{
		            $img = addslashes($_FILES['file']['tmp_name']);
		            $img = file_get_contents($img);
		            $img = base64_encode($img);
		        }


				if(empty($username)){
					$formError[] = "User Name Must Not be Empty!";
				}else{
					$chkuser = checkUser($username,"AND userid != ".$_SESSION['userid']);
					if($chkuser > 0){
						$formError[] = "User Name Already Exist!";
					}
				}
				if(empty($fullname)){
					$formError[] = "Name Must Not Be Empty!";
				}
				if(empty($email)){
					$formError[] = "Email Must Not Be Empty!";
				}

				if(empty($password)){
					$formError[] = "Password Must Not Be Empty!";
				}
					
				if(sha1($oldpass) != $userinfo['password']){

					$formError[] = "Invalid User Password!";

				}
				if(count($formError) <= 0){
					$password = sha1($password);

					//update information

					$stmt = $con->prepare("UPDATE users SET username=?, fullname=?, img=?, email=?, password=?
										   WHERE userid = ?");
					$stmt->execute(array($username,$fullname,$img,$email,$password,$_SESSION['userid']));
					
						$success = "Information Update Successful!";
					

				}


			}
			/* Edit Main Information */
			$userinfo = getUserInfo($_SESSION['userid']);
			include 'profileHeader.php';	
			
			?>
			<h2 class="text-center">Main Information</h2>
			<div  class="container">
				<div class="panel panel-primary">
					<div class="panel-heading"><i class="fa fa-edit"></i> Edit Information</div>
					<div class="panel-body edit-info">
						
						<form enctype="multipart/form-data" action="<?php echo $_SERVER['PHP_SELF'] . '?action=main'; ?>" method="POST">
							<div class="form-group">
								<label class="form-label">User Name</label>
								<input 
									type="text" 
									value="<?php 
												if(isset($username)){
													echo $username;
												}else{
													echo $userinfo['username'];	
												}
											?>" 
									name="username" 
									placeholder="User Name "
									class="form-control"
									required = "required"

								/>
							</div>
							<div class="form-group">
								<label class="form-label">Full Name</label>
								<input 
									type="text" 
									value="<?php 
												if(isset($fullname)){
													echo $fullname;
												}else{
													echo $userinfo['fullname'];	
												}
											?>" 
									name="fullname" 
									placeholder="Full Name "
									class="form-control"
									required ='required'
								/>
							</div>
							<div class="form-group">
								<label class="form-label">Email Address</label>
								<input 
									type="email" 
									value="<?php 
												if(isset($email)){
													echo $email;
												}else{
													echo $userinfo['email'];	
												}
											?>" 
									name="email" 
									placeholder="Email Address "
									class="form-control"
									required="required"
								/>
							</div>

							<div class="form-group">
								<label class="form-label">Old-Password</label>
								<input 
									type="password" 
									name="odlpassword" 
									placeholder="Old Password"
									class="form-control password onChange"
									autocomplete="new-password" 
								/>
								<i style="top:35px;" class="fa fa-eye show-pass"></i>
							</div>

							<div class="form-group">
								<label class="form-label">New-Password</label>
								<input 
									type="password" 
									name="password" 
									placeholder="New Password"
									class="form-control password"
									autocomplete="new-password" 
								/>
								<i style="top:35px;" class="fa fa-eye show-pass"></i>
							</div>

							<div class="form-group">
								<label class="form-label">Profile Image</label>
								<input 
									type="file" 
									name="file"
									size="2" 
									class="form-control"
								/>
								
							</div>

							<input type="submit" value="Save" class="btn btn-success changed" />

						</form>

						<div class="error-info">

							<?php

								foreach($formError as $error){
									echo '<div class="form-alert error-form"> ' . $error . ' </div>';
								}
								if(!empty($success) && count($formError) <= 0){
									echo '<div class="form-alert form-success"> ' . $success . ' </div>';
								}

							?>

						</div>


					</div>
				</div>

			</div>




<?php	}elseif($action == 'data'){

			$success = "";
			$formError = array();
			if($_SERVER['REQUEST_METHOD'] == "POST"){
				
				$name 		= filter_var($_POST['name'],FILTER_SANITIZE_STRING);
				$price 		= "$" . filter_var($_POST['price'],FILTER_SANITIZE_NUMBER_INT);
				$country 	= filter_var($_POST['country'],FILTER_SANITIZE_STRING);
				$desc 		= filter_var($_POST['description'],FILTER_SANITIZE_STRING);
				$status 	= filter_var($_POST['status'],FILTER_SANITIZE_NUMBER_INT);
				$category 	= filter_var($_POST['category'],FILTER_SANITIZE_NUMBER_INT);

				$img = addslashes($_FILES['updateImg']['tmp_name']);
				$img = file_get_contents($img);
				$img = base64_encode($img);
				usleep(200000);
				$chk = getimagesize($_FILES['updateImg']['tmp_name']);
				if($chk == FALSE){
					$formError[] = "plze choose product Image!";
				}

				if(empty($name)){
					$formError[] = "empty item title !";
				}
				if(empty($price)){
					$formError[] = "empty item price !";
				}
				if(empty($country)){
					$formError[] = "empty item country of made !";
				}
				if(empty($desc)){
					$formError[] = "empty item description !";
				}elseif(strlen($desc) < 20){
					$formError[] = "item description must be greater than 20 char !";
				}
				if($status == 0){
					$formError[] = "Choose item status !";
				}
				if($category == 0){
					$formError[] = "choose item category !";
				}

				if(count($formError) <= 0){
					$itemid 	= $_GET['id'];

					$stmt = $con->prepare("UPDATE items SET 
											name=?,
											price=?,
											description=?,
											country_made=?,
											img = ?,
											status=?,
											cat_id=? 
											WHERE id=?");
					$stmt->execute(
						array(
							$name,
							$price,
							$desc,
							$country,
							$img,
							$status,
							$category,
							$itemid
					));

					$success = "Item info update successful!";
				}

			}

			/* Edit Posts Data */

			$itemid = (isset($_GET['id']) && is_numeric($_GET['id'])) ? intval($_GET['id']):0;
			if($itemid > 0){

				$stmt = $con->prepare('SELECT * FROM items WHERE id=? AND user_id=? LIMIT 1');
				$stmt->execute(array($itemid,$_SESSION['userid']));
				$chk = $stmt->rowCount();
				$res = $stmt->fetch(PDO::FETCH_ASSOC);
				if($chk > 0){ ?>
					
					<h2 class="text-center">Edit Post</h2>
					<div class="container">

						<div class="panel panel-primary">
							<div class="panel-heading"><i class="fa fa-edit"></i> Edit Post</div>
							<div class="panel-body">

								<div class="col-sm-3">
									
									<img class="img-responsive block-center img-thumbnail preview" style="height: 220px" src="data:image;base64,<?php echo $res['img'] ?>" alt="live preview">
									
										<input form="update" type="file" name="updateImg" class="form-control">
									
								</div>

								<div class="col-sm-9">
									
									<form enctype="multipart/form-data" id="update" action="edit.php?action=data&id=<?php echo $itemid; ?>" method="POST">

										<div class="form-group">
											<label class="form-label">Title</label>
											<input 
												   type="text"
												   name="name"
												   placeholder="Item Title"
												   value="<?php
												   		echo $res['name'];

												    ?>" 
												   class="form-control"
											/>
										</div>

										<div class="form-group">
											<label class="form-label">Price</label>
											<input 
												   type="text"
												   name="price"
												   placeholder="Item price"
												   value="<?php
												   		echo $res['price'];

												    ?>" 
												   class="form-control"
											/>
										</div>

										<div class="form-group">
											<label class="form-label">Country Of Made</label>
											<input 
												   type="text"
												   name="country"
												   placeholder="Made In"
												   value="<?php
												   		echo $res['country_made'];

												    ?>" 
												   class="form-control"
											/>
										</div>

										<div class="form-group">
											<label class="form-label">Description</label>
											<textarea 
													name="description" 
													placeholder="Item Title"
													class="form-control"
													rows="3" 
													><?php echo $res['description']; ?>
											</textarea> 
												   
										</div>


										<div class="form-group">
					                        <select class="form-control" name="status" >
					                            <option value="0">Choose Status</option>
					                            <option value="1">New</option>
					                            <option value="2">Like New</option>
					                            <option value="3">Used</option>
					                            <option value="4">Old</option>
					                        </select>
					                    </div>
					                    <div class="form-group">
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


					                    <input type="submit" value="Save" class="btn btn-success btn-block">
									</form>

									<div class="error-info">

										<?php

											foreach($formError as $error){
												echo '<div class="form-alert error-form"> ' . $error . ' </div>';
											}
											if(!empty($success) && count($formError) <= 0){
												echo '<div class="form-alert form-success"> ' . $success . ' </div>';
											}

										?>

									</div>


								</div>

							</div>

						</div>
					</div>
					

<?php 		
				}else{ // item check
					header('location:index.php');
					exit();
				}

			}else{ // item check
				header('location:index.php');
				exit();
			}
		}else{ // action end if
			header('location:index.php');
			exit();
		}
	}else{// session end if
		header('location:index.php');
		exit();
	}

	include $templates . 'footer.php';
	ob_end_flush();