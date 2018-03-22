<?php
	ob_start();
	session_start();
	$pageTitle="Item";
	include 'init.php';
	$itemid = (isset($_GET['id']) && is_numeric($_GET['id'])) ? intval($_GET['id']) : 0;
	if($itemid > 0){
		$item = getItemInfo($_GET['id']);
		if(count($item) > 0){	

			if($_SERVER['REQUEST_METHOD'] == "POST"){
				if(isset($_SESSION['user'])){
					
					$comment = filter_var($_POST['message'],FILTER_SANITIZE_STRING);
					$itemId = intval($_GET['id']);
					$userId = intval($_SESSION['userid']);

					$stmt = $con->prepare("INSERT INTO comments (comment,status,item_id,user_id,date) 
										   VALUES (?,?,?,?,now())");
					$stmt->execute(array($comment,0,$itemId,$userId));

				}else{
					echo "You must login first!";
				}
			}

		$userComments = getUsersComments($_GET['id'],1);
?>

<div class="container">
	<h2 class="text-center"><?php echo $item['name']; ?></h2>
	<div class="row">


		<div class="col-md-3">
			<div class="item-img">
				<img class="img-responsive center-block"
					src="data:image;base64,<?php echo $item['img']; ?>" 
					style="height: 220px;"
					alt="product image">
			</div>
		</div>


		<div class="col-md-9">

			<div class="item-info">

				<h3><?php echo $item['name'] ; ?></h3>
				<p><?php echo $item['description'] ; ?></p>
				<ul class="list-unstyled">
					<li><span class="text-capitalize">Posted In </span> <?php echo $item['add_date'] ; ?></li>
					<li><span class="text-capitalize">Price : </span><?php echo $item['price'] ; ?></li>
					<li><span class="text-capitalize">Made In : </span><?php echo $item['country_made'] ; ?></li>
					<li>
						<?php 
							for($i=1;$i<=5;$i++){

								if($i<= intval($item['rating'])){
									echo "<i class='fa fa-star'></i>";
								}else{
									echo "<i class='fa fa-star-o'></i>";
								}
							}

						?>

					</li>
					<li> <span class="text-capitalize">Owner : </span><a href="profile.php?id=<?php echo $item['user_id'] ?>"> <?php echo $item['username']; ?></a></li>
					<li> <span class="text-capitalize">Category : </span><?php echo $item['catName']; ?></li>
				</ul>
			</div>

		</div>

	</div>
	<hr/>


	<div class="row new-comment">
		<div class="col-md-3"></div>
		<div class="col-md-6">
			<form action="<?php echo $_SERVER['PHP_SELF'] . '?id='. $_GET['id'];  ?>" method="POST">
				<textarea class="form-control" name="message" rows="6" placeholder="type your message"></textarea>

				<input type="submit" value="Send" class="btn btn-primary btn-block">

			</form>
		</div>
	</div>

	<?php
		foreach ($userComments as $userComment) {
			
			echo '<div class="row comments">';

				echo '<div class="col-xs-3 col-md-1">';
					echo'<img class="img-responsive img-circle center-block" 
								src="data:image;base64,' . $userComment['img'] . '"
								style="height:60px;width:60px"
								alt="users" >';
				echo'</div>';

				echo'<div class="col-xs-9 col-md-11">';
					echo'<div class="user-comment"> ' . $userComment['comment'] . ' </div>';
				echo'</div>';
			echo'</div>';
		}
	?>

</div>


<?php
		}else{
			echo "<div class='container'><div class='alert alert-danger text-center'>";
			echo "There is no such ID !";
			echo "</div></div>";
		}
	}else{
		echo "<div class='container'><div class='alert alert-danger text-center'>";
		echo "Invalid Item ID !";
		echo "</div></div>";
	}
	include $templates . "footer.php";
	ob_end_flush();
?>