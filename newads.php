<?php
	ob_start();
	$pageTitle= "Create Ad";
	session_start();
	include 'init.php';

	if(isset($_SESSION['user'])){ 

		$errors = array();
		if($_SERVER['REQUEST_METHOD'] == "POST"){
			$success = "";
            $name    = filter_var($_POST['name'],FILTER_SANITIZE_STRING);
            $des     = filter_var($_POST['description'],FILTER_SANITIZE_STRING);
            $price   = "$". filter_var($_POST['price'],FILTER_SANITIZE_NUMBER_INT);
            $country = filter_var($_POST['country'],FILTER_SANITIZE_STRING);
            $status  = $_POST['status'];
            $cat    = $_POST['category'];
            $userid = $_SESSION['userid'];

            usleep(200000);
            $chk = getimagesize($_FILES['productImg']['tmp_name']);
            if($chk == False){
            	$errors[] = "plze choose your product image!";
            }else{
            	$img = addslashes($_FILES['productImg']['tmp_name']);
            	$img = file_get_contents($img);
            	$img = base64_encode($img);
            }


            if(empty($name)){
                $errors[] = "Name Of Itme Must Not Be Empty!";
            }
            if(empty($des)){
                $errors[] = "Description Of Item Must Not Be Empty!";
            }if(empty($price)){
                $errors[] = "Price Of Item Must Not Be Empty!";
            }if(empty($country)){
                $errors[] = "Country Of Made Must Not Be Empty!";
            }
            if($status == 0){
                $errors[] = "You Must Choose The Item Status!";
            }
            if($cat == 0){
                $errors[] = "You Must Choose The Category Of The Item!";
            }

            if(empty($errors)){
                $stmt = $con->prepare("INSERT INTO items(
                	name,
                	description,
                	price,
                	country_made,
                	status,
                	user_id,
                	cat_id,
                	img,
                	add_date

                )VALUES (?,?,?,?,?,?,?,?,now())");
                $stmt->execute(array(
                	$name,
                	$des,
                	$price,
                	$country,
                	$status,
                	$userid,
                	$cat,
                	$img
                ));

                $success= "Item Added Successful!";
                $name = "";
				$des= "";
				$price ="";
				$country="";

            }
		}


?>
		<div class="container">
			<h2 class="text-center">Create Ads</h2>
			<div class="row">
				<div class="col-md-8">
					<form enctype="multipart/form-data" class="form-ads" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">

						<div class="form-group">
	                        <input 
	                        	data-class="title"
	                            type="text" 
	                            value="<?php echo (isset($name))? $name : ''; ?>" 
	                            class="form-control" 
	                            placeholder="Item Name" 
	                            name="name"
	                            required="required" 
	                             />
	                        <span class="require">*</span>
	                    </div>

	                    <div class="form-group">
	                       
	                        <input 
	                        	data-class="description"
	                            type="text" 
	                            class="form-control" 
	                            value="<?php echo (isset($des))? $des : ''; ?>"
	                            placeholder="Item Description" 
	                            name="description" 
	                            required="required"
	                             />
	                        <span class="require">*</span>
	                    </div>
	                    <div class="form-group">
	              
	                        <input 
	                        	data-class="price"
	                            type="text" 
	                            class="form-control" 
	                            value="<?php echo (isset($price))? $price : ''; ?>"
	                            placeholder="Item Price" 
	                            name="price" 
	                            required = "required"
	                             />
	                        <span class="require">*</span>
	                    </div>
	                    <div class="form-group">
	                       
	                        <input 
	                            type="text" 
	                            class="form-control" 
	                            placeholder="Country of made" 
	                            value="<?php echo (isset($country))? $country : ''; ?>"
	                            name="country" 
	                            required="required"
	                             />
	                        <span class="require">*</span>
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
	                    <div class="form-group">
	                    	<label class="label-control">Choose Image</label>

	                    	<input type="file" name="productImg" size="1" class="form-control" />

	                    </div>
	                    <div class="form-group">

	                        <input type="submit" value="Add Item" class="btn btn-block  btn-primary" />

	                    </div>



					</form>

				</div>

				<div class="col-md-4">

					<div class="live-preview">
						<div class="box-gallary">
		                    <span class="price preview-price">$0</span>
		                    <div class="img">
		                        <img class="img-responsive center-block preview" style="height: 220px;" src="" alt=""/>
		                    </div>
		                    <div class="item-info text-center">
		                        <h3 class="preview-title">Title</h3>
		                        <p class="text-muted preview-description">description</p>
		                    </div>
		                </div>
		            </div>

				</div>
				

			</div>

			<?php
				foreach ($errors as $error) {
				
					echo '<div style="margin-left:0;" class="error-form form-alert">' . $error . '</div>';
				}

				if(!empty($success)){

					echo '<div class="row">
							<div class="col-md-8">
								<div class="form-success">' . $success . '</div>
							</div>
						</div>';

				}

				?>

		</div>



<?php
	}else{
		header("location:index.php");
		exit();
	}

	include $templates ."footer.php";
	ob_end_flush();

?>