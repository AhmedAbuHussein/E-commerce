<?php

$action = isset($_GET['action']) ? $_GET['action'] : 'manage';

if($action == 'manage'){
    echo 'welcome in manage page';
}elseif($action == "add"){
    echo 'welcome in add page';
}elseif($action == 'insert'){
    echo 'welcome in insert page';
}