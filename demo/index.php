<!DOCTYPE html>
<html>
    <head>
        <title>YerForm Demo</title>
        <meta charset="utf-8"/>
        
        <link rel="stylesheet" href="css/demo.css" type="text/css" />
        <style rel="stylesheet" type="text/css">
            
        </style>
        
        <?php

            function __autoload( $class_name ) {
                
                if ( $class_name == 'yerForm' ) require_once( '../core/yerform.php');
            }
            
            if ( class_exists('yerForm') ) {
            
                echo '<link rel="stylesheet" type="text/css" href="../yerform/themes/default/yerform-styles.css" />';
            }
            
        ?>
        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
        <script src="js/script.js" type="text/javascript"></script>
        <script type="text/javascript">
        
        </script>

        <!--[if IE]>
            <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
        <![endif]-->

    </head>
    <body>
        
        <section id="wrap">

            <?php
            
                $form = new yerForm();
                $form->form_id = 'my_contact_form';
            
            
            ?>

        </section>
        
    </body>
</html>
