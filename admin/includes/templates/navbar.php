

<nav class="navbar navbar-inverse">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-nav" aria-expanded="false">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="dashboard.php"><?php echo lang('HOME') ?></a>
        </div>
        <div class="collaps navbar-collaps" id="app-nav">
            <ul class="nav navbar-nav">
                <li><a href="category.php"><?php echo lang('CATEGORIES') ?></a></li> 
                <li><a href="items.php"><?php echo lang('ITEMS') ?></a></li> 
                <li><a href="members.php"><?php echo lang('MEMBERS') ?></a></li> 
                <li><a href="comments.php"><?php echo lang('COMMENTS') ?></a></li> 
                <li><a href="#"><?php echo lang('STATISTICS') ?></a></li> 
                <li><a href="#"><?php echo lang('LOGS') ?></a></li> 
            </ul>

            <ul class="nav navbar-nav navbar-right">
                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#"><?php echo $_SESSION['admin'] ?>
                    <span class="caret"></span></a>
                    <ul class="dropdown-menu">
                    <li><a href="members.php?action=edit&id=<?php echo $_SESSION['id'] ?>"><?php echo lang("EDIT_PROFILE") ?></a></li>
                    <li><a href="../index.php"><?php 
                        
                            $_SESSION['user'] =$_SESSION['admin'] ;
                            $_SESSION['userid'] = $_SESSION['id'];
                            echo lang('SHOP') ?>
                                
                        </a>
                    </li>
                    <li><a href="#"><?php echo lang('SETTING') ?></a></li>
                    <li><a href="logout.php"><?php echo lang('LOG_OUT') ?></a></li> 
                    </ul>
                </li>
            </ul>
        </div>
  </div>
</nav>