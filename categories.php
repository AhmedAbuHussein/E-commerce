<?php  
    ob_start();
    session_start();
    $pageTitle = "Shop";
    include "init.php"; 
?>

<div class="container">
    <h2 class="text-center"><?php echo str_replace("-"," ",$_GET['catname']); ?></h2>
    <div class="row">
        <?php 
            $items = getItems($_GET['catid']);
            $i=1;
            foreach($items as $item){
                if($i > 11) $i=0;
                echo "<div class='col-sm-6 col-md-3'>";
                    echo "<div class='thumbnail box-items'>";
                        echo "<span class='price'>" . $item['price'] . "</span>";

                        echo "<img class='img-responsive' style='height:220px;' 
                        src='data:image;base64," . $item['img']. "' alt='image' />";
                        
                        echo "<div class='caption'>";
                            echo "<h3 class='text-center'><a href='show.php?id=" . $item['id'] . "'>" . $item['name'] . "</a></h3>";
                            echo "<p class='lead text-muted description'>" . $item['description'] . "</p>";
                        echo "</div>";
                    echo "</div>";
                echo "</div>";
                $i+=1;
            }
        ?>
    </div>
</div>

<?php include $templates . "footer.php"; ?>