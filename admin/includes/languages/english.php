<?php

 function lang( $word )
 {
     static $lang = array(

        'HOME'          =>'Home',
        'CATEGORIES'    =>'Categories',
        'ITEMS'         =>'Items',
        'MEMBERS'       =>'Members',
        'COMMENTS'       =>'Comments',
        'STATISTICS'    =>'Statistics',
        'LOGS'          =>'Logs',
        'EDIT_PROFILE'  => 'Edit profile',
        'SHOP'  => 'Visit Shop',
        'SETTING'       =>'Setting',
        'LOG_OUT'       => 'Log out',

     );

     return $lang[$word];
 }