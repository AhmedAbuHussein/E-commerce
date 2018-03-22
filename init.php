<?php

    //data base connection
    include 'admin/connect.php';

    //Routing 
    // templates path
    $templates = "./includes/templates/";
    //libs path
    $libs = "./includes/libs/";
    //languages path
    $languages = "./includes/languages/";
    //functions path
    $functions = "./includes/functions/";


    //css path
    $css = "./layout/css/";
    //javascript path
    $js = "./layout/js/";
    //images site path
    $pathImg = "./layout/img/";

    //import important files
    include $languages. 'english.php';
    include $functions . "functions.php";
    include $templates . "header.php";
