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
                
                if ( $class_name == 'YerForm' ) require_once( '../core/yerform.php');
            }
            
            if ( class_exists('YerForm') ) {
            
                echo '<link rel="stylesheet" type="text/css" href="../themes/default/yerform-styles.css" />';
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
            
                $form = new YerForm();
                $form->form_id = 'contact_form';
            
                // TEXT {
                    
                    // EN-US {

                        $form->text['en-US']['message_error_main'] = array( 'typ'=>'error', 'text'=>'Could not send form! Check the following fields: {fields}' );
                        $form->text['en-US']['message_sending'] = array( 'typ'=>'info', 'text'=>'Sending!' );
                        $form->text['en-US']['message_sent'] = array( 'typ'=>'info', 'text'=>'Sent!' );
                        $form->text['en-US']['message_checkdate'] = 'date does not exists';
                        $form->text['en-US']['message_dateformat'] = 'please format the date like 01.06.2013';
                        $form->text['en-US']['messages_validation'] = array(
                            'required' => array( 'text' => 'required' ),
                            'email' => array( 'text' => 'invalid' )
                        );
                        
                        $form->text['en-US']['fieldset']['require_info'] = array( 'text' => 'Fields marked with {require_symbol} are required.' );
                        $form->text['en-US']['fieldsets']['kontaktdaten']['legend'] = 'Contactdata';
                        $form->text['en-US']['fieldsets']['kontaktdaten']['require_info'] = array( 'text' => 'Fields marked with {require_symbol} are required.' );
                        
                        $form->text['en-US']['fields']['surename']['label'] = 'Surename';
                        $form->text['en-US']['fields']['familyname']['label'] = 'Familyname';
                        $form->text['en-US']['fields']['email']['label'] = 'E-Mail';
                        $form->text['en-US']['fields']['plz_ort']['label'] = 'ZIP|City';
                        $form->text['en-US']['fields']['telefon']['label'] = 'Telefon';
                        $form->text['en-US']['fields']['nachricht']['label'] = 'Message';
                        
                        $form->text['en-US']['buttons']['submit']['label'] = 'Submit';
                        $form->text['en-US']['buttons']['reset']['label'] = 'Cancel';
                        
                    // }
                
                // CONFIG {
                
	                $form->config( array(
                        'form_class' => false,
                        'action' => false,
                        'sent_page' => false,
                        'honeypot' => 'Honeypot',
                        'language' => 'en-US',
                        'call_function_on_validation_is_true' => false,
                        'mail_form' => true,
                        'mail_subject' => 'Contact',
                        'field_sender_mail' => 'email',
                        'fields_sender_name' => array( 'surename', 'familyname' ),
                        'recipient_mail' => 'mail@johannheyne.de',
                        'recipient_name' => 'Johann Heyne',
                        'mail_text' => "
                            Contactform
                            Name: {surename} {familyname}
                            E-Mail: {email}
                            Message:
                            {message}
                        "
                    ));
                
                // }
                
                $form->set( 'messages' );
            
	            $form->set( 'fieldset_begin', array(
                    'name'=>'Contactform'
                ));
                    $form->set( 'list_begin', array(
                        'list-layout' => 'block'
                    ));

                        /* fields and field groups */
                    
                        $form->set( 'field_text', array(
                            'name' => 'surename'
                        ));
                        
                        $form->set( 'field_text', array(
                            'name' => 'familyname'
                        ));
                    
                        $form->set( 'field_text', array(
                            'name' => 'email'
                        ));
                    
                    
                        $form->set( 'field_textarea', array(
                            'name' => 'message',
                            'label' => 'Message'
                        ));
                    

                    $form->set( 'list_end' );
                $form->set( 'fieldset_end' );
            
                $form->set( 'form_buttons', array(
                    'submit' => true,
                    'reset' => true
                ));
            
                $form->run( array(
                    'output' => 'echo'
                ));
                
            // }
            
            
            ?>

        </section>
        
    </body>
</html>
