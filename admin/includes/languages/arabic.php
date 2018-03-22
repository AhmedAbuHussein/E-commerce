<?php

 function lang( $word )
 {
     static $lang = array(

        'message'=>'مرحبا',
        'admin'=>'مدير',

     );

     return $lang[$word];
 }