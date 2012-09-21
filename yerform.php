<?php
    
    /**
    * yerform
    *
    * an e-mail form class for PHP 5.2.0 or newer
    *
    * @author       Johann Heyne
    * @copyright    Copyright (c) Johann Heyne
    * @license      MIT
    * @link         https://github.com/johannheyne/yerform
    */
    
    class yerForm {
        
        protected $list_before = '<ul>';
        protected $list_after = '</ul>';
        protected $list_item_before = '<li>';
        protected $list_item_after = '</li>';
        protected $label_before = '<div class="label">';
        protected $label_after = '</div>';
        protected $fields_before = '<div class="fields">';
        protected $fields_after = '</div>';
        protected $field_before = '<div class="field">';
        protected $field_after = '</div>';
        protected $depht = 1;
        protected $code = '';
        protected $request = false;
        protected $files = false;
        protected $sent = false;
        protected $set = false;
        protected $fields = false;
        protected $config = false;
        
        public $form_id = 'yerform';
        public $field_text_size = 40;
        public $field_text_maxlength = 200;
        public $field_textarea_cols = 70;
        public $field_textarea_rows = 7;
        public $required_label_sufix = '<span class="required">*</span>';
        public $messages = false;
        public $validation = false;
        

        public function __construct() {

            $this->config['honeypot'] = false;

            if ( $_REQUEST AND isset( $_REQUEST['submit'] ) ) {
                
                foreach ( $_REQUEST as $key => $value ) {
                   $this->request[ $key ] = $this->sanitize( $value );
                }
                
                 $this->files = $_FILES;
            }
        }
        
        
        
        /** 
        * sanitizing
        */
        
        protected function sanitize( $string ) {
            $string = strip_tags( $string );
            $string = htmlspecialchars( $string, ENT_QUOTES );
            return $string;
        }
        
        
        
        /** 
        * configuration
        */

        public function config( $p = array() ) {

            $p += array(
                'form_class' => false,
                'action' => false,
                'sent_page' => false,
                'honeypot' => false,
                'mail_subject' => false,
                'sender_mail' => false,
                'sender_name' => false,
                'field_sender_mail' => false,
                'fields_sender_name' => false,
                'recipient_mail' => false,
                'recipient_name' => false,
                'mail_text' => false,
                'sender_mail' => false,
                'sender_name' => false,
                'message_error_main' => array( 'typ'=>'error', 'text'=>'The formular could not be send!' ),
                'message_mail_sending' => array( 'typ'=>'info', 'text'=>'The e-mail is sending!' ),
                'message_mail_sent' => array( 'typ'=>'info', 'text'=>'The e-mail was sent!' ),
                'message_honeypot' => array( 'typ'=>'info', 'text'=>'Yer cheating!' )
            );

            foreach ( $p as $key => $value ) {
                
                if ( $value !== false ) $this->config[$key] = $value;
            }

        }
        
        
        
        /** 
        * collecting all formparameter.
        */

        public function set( $f , $p = array() ) {

            $p += array(
                'display' => true
            );

            $this->set[] = array(
                'f' => $f,
                'p' => $p
            );

            if (
                $f === 'field_hidden' OR
                $f === 'field_text' OR
                $f === 'field_textarea' OR
                $f === 'field_select' OR
                $f === 'field_checkbox' OR
                $f === 'field_file' OR
                $f === 'field_date'
            ) {
                $this->fields[ $p['name'] ] = $p;
            }
        }
        
        
        
        /** 
        * runs the workflow ( validation, sending, formbuilding )
        */

        public function run() {

            // if request
            if ( $this->request ) {

                // do validation
                $this->validation();

                // if not valid set error message
                if ( $this->validation !== false ) {

                    $this->messages['message_error_main'] = true;
                }

                // if valid then send mail and build message
                if ( $this->validation === false ) {

                    $this->send_mail();
                }
            }


            // mail sending
            if ( $this->sent === true ) {
            
                $_GET['ajax'] = 'y';
                
                if ( !$_GET['ajax'] ) {
                    echo '<meta http-equiv="refresh" content="0; URL=' . $this->config['sent_page'] . '?sent=true">';
                    $this->messages['message_mail_sending'] = true;
                    $this->messages();
                
                    echo $this->get_form();
                }
                else {
                    
                    $this->messages['message_mail_sent'] = true;
                    $this->messages();

                    echo $this->get_form();
                }
                
            }


            // mail sent
            elseif ( $_GET AND isset( $_GET['sent'] ) AND $_GET['sent'] === 'true' ) {

                $this->messages['message_mail_sent'] = true;
                $this->messages();
                
                echo $this->get_form();
            }
            // if not send show form
            else {

                // honeypot
                if ( $this->config['honeypot'] ) {
                    
                    $this->list_begin();
                    
                    $this->field_text( array(
                        'name' => strtolower( $this->config['honeypot'] ),
                        'label' => $this->config['honeypot'],
                        'class' => strtolower( $this->config['honeypot'] )
                        )
                    );
                    
                    $this->list_end();
                }

                // walk the form settings
                foreach( $this->set as $key => $item) {
                    if ( $item['p']['display'] === true ) {

                        if ( $item['f'] === 'field_hidden' )    $this->field_hidden( $item['p'] );
                       
                    }
                }
                
                foreach( $this->set as $key => $item) {

                    if ( $item['p']['display'] === true ) {

                        if ( $item['f'] === 'list_begin' )      $this->list_begin( $item['p'] );
                        if ( $item['f'] === 'list_end' )        $this->list_end();
                        if ( $item['f'] === 'group_begin' )     $this->group_begin( $item['p'] );
                        if ( $item['f'] === 'group_end' )       $this->group_end();
                        if ( $item['f'] === 'field_text' )      $this->field_text( $item['p'] );
                        if ( $item['f'] === 'field_textarea' )  $this->field_textarea( $item['p'] );
                        if ( $item['f'] === 'field_select' )    $this->field_select( $item['p'] );
                        if ( $item['f'] === 'field_checkbox' )  $this->field_checkbox( $item['p'] );
                        if ( $item['f'] === 'field_date' )      $this->field_date( $item['p'] );
                        if ( $item['f'] === 'field_file' )      $this->field_file( $item['p'] );
                        if ( $item['f'] === 'field_html' )      $this->field_html( $item['p'] );
                        if ( $item['f'] === 'form_buttons' )    $this->form_buttons( $item['p'] );
                        if ( $item['f'] === 'fieldset_begin' )  $this->fieldset_begin( $item['p'] );
                        if ( $item['f'] === 'fieldset_end' )    $this->fieldset_end( $item['p'] );
                        if ( $item['f'] === 'require_info' )    $this->require_info( $item['p'] );
                        if ( $item['f'] === 'messages' )        $this->messages( $item['p'] );
                    }
                }

                echo $this->get_form();
            }
        }
        
        
        
        /** 
        * validation
        */

        protected function validation() {
            
            /* go thru each item of set */
            foreach( $this->set as $num => $field ) {

                $f = $field['f'];
                $p = $field['p'];

                /* issit a field for validation */
                if ( 
                    $f === 'field_text' OR
                    $f === 'field_date' OR  
                    $f === 'field_file' OR  
                    $f === 'field_select'   
                ) {

                /* are there validations for this field */
                if ( isset( $p['validation'] ) ) {

                    /* go thru each validation-rule of the field */
                    foreach( $p['validation'] as $key => $valid ) {

                        // type of required, disables all other validations rules 
                        if ( $valid['type'] === 'required' AND $valid['cond'] === true ) {
                            if ( !isset( $this->request[ $p['name'] ] ) AND $this->files[ $p['name'] ]['error'] !== 0  ) $this->validation[ $p['name'] ][] = $valid['message'];
                            if ( isset( $this->request[ $p['name'] ] ) AND $this->request[ $p['name'] ] === '' ) $this->validation[ $p['name'] ][] = $valid['message'];
                        }

                        // all other validation rules 
                        if ( !isset( $this->validation[ $p['name'] ] ) ) {

                            // if
                            if ( $valid['type'] === 'if' ) {

                                if ( $this->ifit( $valid['value'], $valid['operator'], $this->request[ $p['name'] ] ) ) $this->validation[ $p['name'] ][] = $valid['message'];
                            }

                            // expression
                            if ( $valid['type'] === 'expression' ) {
                                if ( !ereg( $valid['cond'], $this->request[ $p['name'] ] ) ) {
                                    $this->validation[ $p['name'] ][] = $valid['message'];
                                }
                            }

                            // date
                            if ( $valid['type'] === 'date' ) {

                                $date = $this->request[ $p['name'] ];
                                $date = explode( '.', $date );

                                $timestamp = mktime( 0, 0, 0, $date[1], $date[0], $date[2]);

                                // dateformat
                                if ( !ereg( "^[0-9]{1,2}\.[0-9]{1,2}\.[0-9]{4}$", $this->request[ $p['name'] ] ) ) {
                                    $this->validation[ $p['name'] ][] = $valid['message-dateformat'];
                                }

                                // checkdate
                                if ( !isset( $this->validation[ $p['name'] ] ) AND !checkdate( $date[1], $date[0], $date[2] ) ) {
                                    $this->validation[ $p['name'] ][] = $valid['checkdate'];
                                }

                                // min-max
                                if ( !isset( $this->validation[ $p['name'] ] ) ) {

                                    if ( isset( $valid['min'] ) OR isset( $valid['max'] )) {
                                        $array = array (
                                            "{min}" => date( "d.m.Y", $this->datestamp( $valid['min'] ) ),
                                            "{max}" => date( "d.m.Y", $this->datestamp( $valid['max'] ) )
                                        );
                                        $valid['message-min-max'] = strtr($valid['message-min-max'], $array);
                                    }

                                    if ( isset( $valid['min'] ) ) {
                                        if ( $timestamp < $this->datestamp( $valid['min'] ) ) $this->validation[ $p['name'] ][] = $valid['message-min-max'];
                                    }
                                    if ( isset( $valid['max'] ) ) {
                                        if ( $timestamp > $this->datestamp( $valid['max'] ) ) $this->validation[ $p['name'] ][] = $valid['message-min-max'];
                                    }
                                }

                                // dependency
                                if ( !isset( $this->validation[ $p['name'] ] ) AND isset( $valid['dependency'] ) ) {

                                    $date_dep = explode( '.', $this->request[ $valid['dependency']['field'] ] );

                                    $timestamp_dep = strtotime( $valid['dependency']['value'] , strtotime( $date_dep[2] . '-' . $date_dep[1] . '-' . $date_dep[0] ) );
                                    $value = $timestamp - $timestamp_dep;
                                    if ( !$this->ifit( $value, $valid['dependency']['operator'], 0 ) ) {
                                        $this->validation[ $p['name'] ][] =  $valid['dependency']['message'];
                                    }
                                }
                            }

                             // integer
                             if ( $valid['type'] === 'integer' ) {
                                 if ( $this->get_field_value( $p ) !== '' ) {
                               
                                    if ( !is_numeric( $this->get_field_value( $p ) ) OR (int)$this->get_field_value( $p ) != $this->get_field_value( $p ) ) {
                                         $this->validation[ $this->get_field_name( $p ) ][] = $valid['message'];
                                     }
                                 }
                             }

                            // range
                            if ( $valid['type'] === 'range' AND !isset( $this->validation[ $this->get_field_name( $p ) ] ) AND $this->get_field_value( $p ) != '' ) {

                                    if ( (float)$this->get_field_value( $p ) < (float)$valid['min'] OR (float)$this->get_field_value( $p ) > (float)$valid['max'] ) {

                                        $array = array (
                                            "{min}" => $valid['min'],
                                            "{max}" => $valid['max']
                                        );
                                        $valid['message'] = strtr($valid['message'], $array);

                                        $this->validation[ $this->get_field_name( $p ) ][] = $valid['message'];
                                    }
                                }
                            }
                        
                            
                            // email
                            if ( $valid['type'] === 'email' ) {
                                if ( $this->get_field_value( $p ) !== '' ) {

                                    if ( !filter_var( $this->get_field_value( $p ), FILTER_VALIDATE_EMAIL ) ) {
                                        $this->validation[ $this->get_field_name( $p ) ][] = $valid['message'];
                                    }
                                }
                            }

                            

                        }
                    }
                }
            }

            /* check honeypot */
            if ( $this->config['honeypot'] AND $this->request[ strtolower( $this->config['honeypot'] ) ] != '' ) {
                $this->validation = true;
                $this->messages['message_honeypot'] = true;
            }

        }
        
        
        
        /** 
        * sending mail
        */

        protected function send_mail() {

            /* sender email */
            $sender_mail = false;
            if ( $this->config['sender_mail'] ) {
                $sender_mail = $this->config['sender_mail'];
            }
            if ( $this->config['field_sender_mail'] ) {
                $sender_mail = $this->request[ $this->config['field_sender_mail'] ];
            }

            /* sender name */
            $sender_name = false;
            if ( $this->config['fields_sender_name'] ) {
                foreach ( $this->config['fields_sender_name'] as $key => $value ) {
                    $sender_name .= $this->request[ $value ] . ' ';
                }
            }
            if ( $this->config['sender_name'] ) {
                $sender_name = $this->config['sender_name'];
            }
            $sender_name = trim( $sender_name );


            /* mail subject */
            $mail_subject = $this->config['mail_subject'];


            /* mailtext */
            $mail_text = $this->config['mail_text'];


            // get off the whitespace of lines
            $mail_text_arr = explode( "\n", $mail_text );
            foreach($mail_text_arr as $key => $value) {
                $mail_text_arr[ $key ]  = trim( $value );
            }
            $mail_text = implode( "\n", $mail_text_arr );

            // fill placeholders
            $array = false;
            foreach($this->fields as $name => $item) {
                $value = $_REQUEST[ $name ];
                if ( is_array($value) ) $value = trim( implode( ', ', $value ), ', ' );
                $array['{' . $name . '}'] = $value;
            }
            $mail_text = trim( strtr($mail_text, $array) );




            // mail it with PHPMailer

            /*require_once 'class.phpmailer.php';
            $mail = new PHPMailer();

            $mail->CharSet = 'utf-8';
            $mail->IsSMTP();
            $mail->SMTPAuth = true;                  // enable SMTP authentication
            $mail->SMTPDebug  = 1;                     // enables SMTP debug information (for testing)
            $mail->Host = "mail.yourdomain.com"; // sets the SMTP server
            //$mail->Port = 587;                    // set the SMTP port for the GMAIL server
            $mail->Username = "mail+reiseservice-africa.de"; // SMTP account username
            $mail->Password = "xnL?bfC2sEpC";        // SMTP account password
            // enables SMTP debug information (for testing)
            $mail->SetFrom( $sender_mail, $sender_name );
            $mail->AddReplyTo( $sender_mail, $sender_name );
            $mail->AddAddress( $this->config['recipient_mail'], $this->config['recipient_name'] );
            $mail->Subject = 'Kontaktformular';
            $mail->Body = eregi_replace( "[\]",'', $mail_text );
            if ( isset($_FILES['file']['tmp_name']) && $_FILES['file']['tmp_name'] !== '' ) $mail->AddAttachment( $_FILES['file']['tmp_name'], $_FILES['file']['name']) ; // attachment
            if ( !$mail->Send() ) {
                print_o($mail->ErrorInfo );
            }
            else {
                $this->sent = true;
                $this->request = false;
            }*/




            // mail it with Swiftmailer

            require_once 'lib/swift_required.php';

            // Create the Transport
                
                // if ( function_exists('proc_open') ) { print_o( 'yes');  } else { print_o( 'no' ); }
            
                /* PHP Mail
                    $transport = Swift_MailTransport::newInstance();
                */
            
                /* Sendmail
                    $transport = Swift_SendmailTransport::newInstance('/usr/sbin/sendmail');
                */
            
                /* Smtp & SSL
                    $transport = Swift_SmtpTransport::newInstance( 'server.net', 123, 'ssl' )
                        ->setUsername( 'post+domain.de' )
                        ->setPassword( '12345678' );
                */
            
            $transport = Swift_MailTransport::newInstance();
            
            // Create the Mailer using your created Transport
            $mailer = Swift_Mailer::newInstance( $transport );

            // Create a message
            $message = Swift_Message::newInstance( $mail_subject )
                ->setFrom( array( $sender_mail => $sender_name ) )
                ->setTo( array( $this->config['recipient_mail'] => $this->config['recipient_name'] ) )
                ->setBody( $mail_text );
            
            // Attache files
            if ( isset($_FILES) ) {
                foreach ( $_FILES as $key => $item ) {
                    if ( $item['tmp_name'] != '' ) {
                        $message->attach( Swift_Attachment::fromPath( $item['tmp_name'] )->setFilename($item['name']) );
                    }
                }
            }
           
            //print_o( $mail_subject . $sender_mail .  $sender_name . $mail_text . $this->config['recipient_mail'] . $this->config['recipient_name'] );
            //print_o( $message );
            
            // Send the message
            $result = $mailer->send( $message );
            if ( $result ) {
                $this->sent = true;
                $this->request = false;
            }

        }
        
        
        
        /** 
        * Gibt gesamtes Formular zurück
        *
        * @vari     code
        */

        protected function get_form() {

            $ret = '';
            $ret .= '<form id="' . $this->form_id . '" class="form ' . $this->config['form_class'] . '" action="' . $this->config['action'] . '" method="post" enctype="multipart/form-data" name="form" target="_self">';
            $ret .= $this->code;
            $ret .= '</form>';
            
            return $ret;
        }
        
        
        
        /** 
        * require_info
        */

        protected function require_info( $p = array() ) {

            $p += array(
                'text' => 'Fields marked with {require_symbol} are reqired.'
            );

            $p['text'] = str_replace( '{require_symbol}', $this->required_label_sufix, $p['text'] );

            $ret = '<p class="require_info">' . $p['text'] . '</p>';
            
            $this->code .= $ret;
        }
        
        
        
        /** 
        * require_info
        */

        protected function messages() {

            $ret = '';
            
            /* get fields of validation error and build a string */
            $fieldnames_string = false;
            
            if ( isset ( $this->validation ) AND is_array( $this->validation ) ) {
                foreach ( $this->validation as $key => $item ) {
                    $fieldnames[] = $this->fields[$key]['label'];
                }
            }
            if ( isset( $fieldnames ) ) $fieldnames_string = implode( ', ', $fieldnames );
            
            /* loop the messages */
            if ( is_array( $this->messages ) ) {
                $ret .= '<div class="messages">';
                foreach($this->messages as $key => $value ) {
                    
                    $message = $this->config[ $key ];
                    
                    if ( $fieldnames_string ) $message = str_replace( '{fields}', $fieldnames_string, $message );
                    
                    $ret .= '<div class="' . $message['typ'] . '">' . $message['text'] . '</div>';
                }
                $ret .= '</div>';
            }
            
            $this->code .= $ret;
        }
        
        
        
        /** 
        * Textfeld
        * gibt den HTML-Code für ein Textfeld aus.
        *
        * @child    get_label()
        * @vari     list_item_before
        * @vari     fields_before
        * @vari     fields_after
        * @vari     code
        */
        
        protected function field_text( $p = array() ) {
            
            $p += array(
                'label' => 'no name', 
                'name' => 'noname',
                'array' => false,
                'size' => $this->field_text_size,
                'maxlength' => $this->field_text_maxlength,
                'padding' => array(0,0),
                'layout' => false
            );
            
            $p['fieldtype'] = 'text';
            
            $ret = '';
            $ret .= $this->list_item_before( $p );
            $ret .= $this->get_label( $p );
            $ret .= $this->fields_before;
            $ret .= $this->field_before;
            $ret .= '<input class="form-field field-margin-right" type="text" id="' . $this->get_field_name( $p ) . '" name="' . $this->get_field_name( $p ) . '" value="' . $this->get_field_value( $p ) . '" size="' . $p['size'] . '" maxlength="' . $p['maxlength'] . '"/>';
            $ret .= $this->field_after;
            $ret .= $this->get_field_sufix( $p );
            $ret .= $this->get_field_messages( $p );
            $ret .= $this->fields_after;
            $ret .= $this->list_item_after();
            $this->code .= $ret;
        }
        
        
        
        /** 
        * Textarea
        */
        
        protected function field_textarea( $p = array() ) {
            
            $p += array(
                'label' => 'no name', 
                'name' => 'noname',
                'array' => false,
                'cols' => $this->field_textarea_cols,
                'rows' => $this->field_textarea_rows,
                'padding' => array(0,0),
                'layout' => false
            );
            
            $p['fieldtype'] = 'textarea';
            
            $ret = '';
            $ret .= $this->list_item_before( $p );
            $ret .= $this->get_label( $p );
            $ret .= $this->fields_before;
            $ret .= $this->field_before;
            $ret .= '<textarea id="' . $this->get_field_name( $p ) . '" name="' . $this->get_field_name( $p ) . '" cols="' . $p['cols'] . '" rows="' . $p['rows'] . '">' . $this->get_field_value( $p ) . '</textarea>';
            $ret .= $this->field_after;
            $ret .= $this->get_field_sufix( $p );
            $ret .= $this->get_field_messages( $p );
            $ret .= $this->fields_after;
            $ret .= $this->list_item_after();
            $this->code .= $ret;
        }
        
        
        
        /** 
        * Select
        * gibt den HTML-Code für ein Textfeld aus.
        *
        * @child    get_label()
        * @vari     list_item_before
        * @vari     fields_before
        * @vari     fields_after
        * @vari     code
        */
        
        protected function field_select( $p = array() ) {
            
            $p += array(
                'label' => 'no name', 
                'name' => 'noname',
                'value' => '',
                'array' => false,
                'padding' => array(0,0),
                'layout' => false,
                'data' => array( '' => 'wähle…' )
            );
            
            $p['fieldtype'] = 'select';
            
            $ret = '';
            $ret .= $this->list_item_before( $p );
            $ret .= $this->get_label( $p );
            $ret .= $this->fields_before;
            $ret .= $this->field_before;
            $ret .= '<select name="' . $this->get_field_name( $p ) . '">';
            foreach($p['data'] as $key => $value) {
                if ( $this->get_field_value( $p ) == $key ) { $selected = ' selected'; } else { $selected = ''; }
                $ret .= '<option value="' . $key . '"' . $selected . '>' . $value . '</option>';
            }
            $ret .= '</select>';
            $ret .= $this->field_after;
            $ret .= $this->get_field_sufix( $p );
            $ret .= $this->get_field_messages( $p );
            $ret .= $this->fields_after;
            $ret .= $this->list_item_after();
            $this->code .= $ret;
        }
        
        
        
        /** 
        * Datumsfeld
        * gibt den HTML-Code f&uuml;r ein Datumsfeld aus.
        *
        * @child    get_label()
        * @vari     list_item_before
        * @vari     fields_before
        * @vari     fields_after
        * @vari     code
        */

        protected function field_date( $p = array() ) {

            $p += array(
                'label' => 'no name', 
                'name' => 'noname',
                'array' => false,
                'size' => $this->field_text_size,
                'maxlength' => $this->field_text_maxlength,
                'padding' => array(0,0),
                'layout' => false,
                'datepicker' => true,
                'datepicker-min' => 0,
                'datepicker-max' => 0,
                'datepicker' => true,
                'validation' => false
            );

            $p['fieldtype'] = 'date';

            $class = '';
            if ( $p['datepicker'] ) $class = ' datepicker';

            /* get the min and max day setings for jquery-datepicker from validation info */
            if ( $p['datepicker'] AND $p['validation'] ) {
                foreach( $p['validation'] as $num => $item ) {
                    if ( $item['type'] === 'date' ) {
                        if ( isset( $item['min'] ) ) {
                            $p['datepicker-min'] = ( $this->datestamp( $item['min'] ) - mktime( 0, 0, 0, date("m"), date("d"), date("Y")) ) / 86400 . 'd';
                        }
                        if ( isset( $item['max'] ) ) {
                            $p['datepicker-max'] = ( $this->datestamp( $item['max'] ) - mktime( 0, 0, 0, date("m"), date("d"), date("Y")) ) / 86400 . 'd';
                        }
                    }
                }
            }

            $data = '';
            if ( $p['datepicker'] ) {
                $data .= ' data-datepicker-min="' . $p['datepicker-min'] . '"';
                $data .= ' data-datepicker-max="' . $p['datepicker-max'] . '"';
            }

            $ret = '';
            $ret .= $this->list_item_before( $p );
            $ret .= $this->get_label( $p );
            $ret .= $this->fields_before;
            $ret .= $this->field_before;
            $ret .= '<input class="form-field field-margin-right' . $class . '" type="text" id="' . $this->get_field_name( $p ) . '" name="' . $this->get_field_name( $p ) . '" value="' . $this->get_field_value( $p ) . '" size="' . $p['size'] . '" maxlength="' . $p['maxlength'] . '"' .  $data . '/>';
            $ret .= $this->field_after;
            $ret .= $this->get_field_messages( $p );
            $ret .= $this->fields_after;
            $ret .= $this->list_item_after();
            
            $this->code .= $ret;
        }
        
        
        
        /** 
        * Checkbox
        * gibt den HTML-Code für eine oder mehrere checkboxen aus.
        *
        * @child    get_label()
        * @vari     list_item_before
        * @vari     fields_before
        * @vari     fields_after
        * @vari     code
        */

        protected function field_checkbox( $p = array() ) {

            $p += array(
                'label' => 'no name', 
                'name' => 'noname',
                'array' => false,
                'data' => 'checked',
                'checked' => false,
                'padding' => array(0,0),
                'layout' => false
            );

            $p['fieldtype'] = 'checkbox';

            if ( $this->get_field_value( $p ) !== '' OR $p['checked'] === true ) { $checked = ' checked'; } else { $checked = ''; }

            $ret = '';
            $ret .= $this->list_item_before( $p );
            $ret .= $this->fields_before;
            $ret .= $this->field_before;
            $ret .= '<input class="form-field field-margin-right" type="checkbox" id="' . $this->get_field_name( $p ) . '" name="' . $this->get_field_name( $p ) . '" value="' . $p['data'] . '"' . $checked . '/>';
            $ret .= $this->field_after;
            $ret .= $this->get_field_messages( $p );
            $ret .= $this->fields_after;
            $ret .= $this->get_label( $p );
            $ret .= $this->get_field_sufix( $p );
            $ret .= $this->list_item_after();
            
            $this->code .= $ret;
        }
        
        
        
        /** 
        * File
        * gibt den HTML-Code für ein Dateifeld aus.
        *
        * @child    get_label()
        * @vari     list_item_before
        * @vari     fields_before
        * @vari     fields_after
        * @vari     code
        */
        
        protected function field_file( $p = array() ) {
            
            $p += array(
                'label' => 'no name', 
                'name' => 'noname',
                'array' => false,
                'size' => $this->field_text_size / 2,
                'padding' => array(0,0),
                'layout' => false
            );
            
            $p['fieldtype'] = 'file';
            
            $ret = '';
            $ret .= $this->list_item_before( $p );
            $ret .= $this->get_label( $p );
            $ret .= $this->fields_before;
            $ret .= $this->field_before;
            $ret .= '<input name="' . $this->get_field_name( $p ) . '" type="file" size="' . $p['size'] . '">';
            $ret .= $this->field_after;
            $ret .= $this->get_field_sufix( $p );
            $ret .= $this->get_field_messages( $p );
            $ret .= $this->fields_after;
            $ret .= $this->list_item_after();
            $this->code .= $ret;
        }
        
        
        
        /** 
        * HTML
        * gibt HTML-Code aus.
        *
        * @child    get_label()
        * @vari     list_item_before
        * @vari     fields_before
        * @vari     fields_after
        * @vari     code
        */
        
        protected function field_html( $p = array() ) {
            
            $p += array(
                'padding' => array(0,0),
                'layout' => false,
                'content' => false
            );
            
            $p['fieldtype'] = 'html';
            
            $ret = '';
            $ret .= $this->list_item_before( $p );
            $ret .= $p['content'];
            $ret .= $this->list_item_after();
            $this->code .= $ret;
        }
        
        
        
        /** 
        * Hidden
        * gibt den HTML-Code für ein Hidden-Feld aus.
        *
        * @child    get_label()
        * @vari     list_item_before
        * @vari     fields_before
        * @vari     fields_after
        * @vari     code
        */
        
        protected function field_hidden( $p = array() ) {
            
            $p += array(
                'name' => 'noname',
                'array' => false,
                'value' => false
            );
            
            $p['fieldtype'] = 'hidden';
            
            $ret = '';

            $ret .= '<input name="' . $this->get_field_name( $p ) . '" type="hidden" value="' . $this->get_field_value( $p ) . '"/>';
            $this->code .= $ret;
        }
        
        
        
        /** 
        * Strukturelemente, Begin und Ende von Listen.
        *
        * @vari     list_before
        * @vari     list_after
        * @vari     code
        */

        protected function list_begin( $p = array() ) {
            
            $p += array(
                'class' => false
            );
    
            $class = false;
            if ( $p['class'] ) $class .= ' ' . $p['class'];

            $this->code .= str_replace('>', ' class="root' . $class . '">', $this->list_before);
        }
        
        protected function list_end() {
        
            $this->code .= $this->list_after;
        }
        
        
        
        /** 
        * Strukturelement, Gruppe.
        *
        * @vari     list_before
        * @vari     list_after
        * @vari     list_item_before
        * @vari     list_item_after
        * @vari     label_before
        * @vari     label_after
        * @vari     code
        */

        protected function group_begin( $p = array() ) {

            $p += array(
                'label' => false,
                'layout' => false,
                'class' => false
            );

            $class = '';
            if ( $p['class'] ) $class = ' ' . $p['class'];

            $this->code .= str_replace('>', ' class="list-item field-group' . $class . '">', $this->list_item_before);
            
            if ( $p['label'] ) {
                $this->code .= str_replace('">', ' group depht-' . $this->depht . '">', $this->label_before);
                $this->code .= '<label>' . $p['label'] . '</label>';
                $this->code .= $this->label_after;
            }
            
            $this->code .= str_replace('">', ' group">', $this->fields_before);

            $class = '';
            if ( $p['layout'] ) $class .= $p['layout'];
            $this->code .= str_replace('>', ' class="' . $class . '">', $this->list_before);

            $this->depht = 2;
        }
        
        protected function group_end() {

            $this->code .= $this->list_after;
            $this->code .= $this->fields_after;
            $this->code .= $this->list_item_after;

            $this->depht = 1;
        }
        
        
        
        /** 
        * "required" Zeichen
        * Gibt Zeichen f&uuml;r "required" Felder zuzr&uuml;ck.
        *
        * @parent   get_label()
        * @vari     required_label_sufix
        */

        protected function get_require_label_sufix( $p = array() ) {

            $check = false;
            
            if ( isset( $p['validation'] ) ) {
                foreach( $p['validation'] as $key => $item ) {
                    if ( $item['type'] === 'required' AND $item['cond'] === true ) $check = $this->required_label_sufix;
                }
            }
            
            return $check;
        }
        
        
        
        /** 
        * Label
        * Gibt den HTML-Code f&uuml;r ein Label aus.
        * F&uuml;gt Zeichen an Label an f&uuml;r "required" Felder.
        *
        * @parent   field_text()
        * @vari     label_before
        * @vari     label_after
        */

        protected function get_label( $p = array() ) {
            
            $p += array(
                'label' => 'no name', 
                'name' => 'noname',
                'label_sufix' => false
            );

            $ret = '';
            $ret .= str_replace('">', ' depht-' . $this->depht . '">', $this->label_before);
            $ret .= '<label for="' . $p['name'] . '">' . $p['label'] . $this->get_require_label_sufix( $p ) . '</label>' . $p['label_sufix'];
            $ret .= $this->label_after;
            
            return $ret;
        }
        
        
        
        /** 
        * Formularbuttons
        * Gibt die Formularbuttons aus.
        *
        * @vari     list_item_before
        * @vari     list_item_after
        */

        protected function form_buttons( $p = array() ) {

            $p += array(
                'submit' => true,
                'submit_label' => 'Submit',
                'submit_class' => false,
                'submit_btn_class' => false,
                'reset' => true,
                'reset_label' => 'Reset',
                'reset_class' => false,
                'reset_btn_class' => false
            );

            $ret = '';
            $ret .= '<div class="form-buttons">';
            if ( $p['reset'] ) $ret .= '<div class="reset ' . $p['reset_class'] . '"><input class="btn-reset ' . $p['reset_btn_class'] . '" name="reset" type="reset" value="' . $p['reset_label'] . '"/></div>';
            $ret .= '<div class="submit ' . $p['submit_class'] . '"><input class="btn-submit ' . $p['submit_btn_class'] . '" name="submit" type="submit" value="' . $p['submit_label'] . '"/></div>';
            $ret .= '</div>';
            
            $this->code .= $ret;
        }
        
        
        
        /** 
        * Gibt List-Item Anfangstag aus
        *
        * @vari     list_item_before
        * @vari     list_item_after
        */

        protected function list_item_before( $p = array() ) {

            $p += array(
                'layout' => false, /* can be horizontal */
                'class' => false,
                'padding' => array( 0, 0 )
            );

            $tag = $this->list_item_before;

            $class = 'field-item';
            
            if ( isset( $this->validation[ $this->get_field_name( $p ) ] ) ) $class .= ' fielderror';
            
            if ( isset($p['fieldtype']) ) $class .= ' field-item-type-' . $p['fieldtype'];
            if ( $p['class'] ) $class .= ' ' . $p['class'];

            $class2 = '';
            if ( $p['layout'] ) $class2 = ' ' . $p['layout'];

            $style = '';
            if ( $p['padding'][0] > 0 ) $style .= 'padding-left: ' . $p['padding'][0] . 'px;';
            if ( $p['padding'][1] > 0 ) $style .= 'padding-right: ' . $p['padding'][1] . 'px;';
            
            $ret = str_replace('>', ' style="' . $style . '" class="' . $class . '">', $tag);
            if ( $this->depht > 1 ) $ret .= '<div class="fields-wrap' . $class2 . '">';
            
            return $ret;
        }

        protected function list_item_after( $p = array() ) {
        
            $ret = '';
            
            if ( $this->depht > 1 ) $ret .= '</div>';
            
            $ret .= $this->list_item_after;
            
            return $ret;
        }
        
        
        
        /** 
        * Feldset, Begin und Ende
        *
        * @vari     code
        */

        protected function fieldset_begin( $p = array() ) {

            $p += array(
                'legend' => 'no titel',
                'class' => false,
                'require_info' => false
            );

            $class = '';
            if ( $p['class'] ) $class = ' class="fieldset ' . $p['class'] . '"';

            $this->code .= '<div' . $class . '>';
            $this->code .= '<fieldset>';
            $this->code .= '<legend class="' . $p['class_legend'] . '">' . $p['legend'] . '</legend>';

            if ( $p['require_info'] ) {
                $this->require_info( $p['require_info'] );
            }
        }

        protected function fieldset_end() {

            $this->code .= '</fieldset></div>';
        }
        
        
        
        /** 
        * Requests
        */

        protected function textfilter( $string ) {

            return stripslashes( $string );
        }
        
        
        
        /** 
        * output a timestamp relative from date and not exact time in sec of now
        */

        public function datestamp( $string ) {

            return strtotime( $string , strtotime( date("Y-m-d") ) );
        }
        
        
        
        /** 
        * if width an given operator
        */

        protected function ifit( $var1, $op, $var2 ) {

            switch ( $op ) {
                case "=":  return $var1 == $var2;
                case "!=": return $var1 != $var2;
                case ">=": return $var1 >= $var2;
                case "<=": return $var1 <= $var2;
                case ">":  return $var1 >  $var2;
                case "<":  return $var1 <  $var2;
                default:       return true;
            }   
        }
        
        
        
        /** 
        * get_fieldname
        */

        protected function get_field_name( $p = array() ) {

            $p += array(
                'array' => false
            );

            $name = $p['name'];

            if ( $p['array'] !== false ) $name .= '[' . $p['array'] . ']'; 

            return $name;
        }
        
    
        
        /** 
        * get_fieldvalue
        */

        protected function get_field_value( $p = array() ) {

            $p += array(
                'array' => false,
                'value' => ''
            );
            
            $value = $p['value'];

            if ( isset($this->request[ $p['name'] ]) ) {
                $value = @$this->request[ $p['name'] ];
                if ( $p['array'] !== false ) $value = @$this->request[ $p['name'] ][ $p['array'] ];
            }
            
            $value = $this->textfilter( $value );
            
            return $value;
        }
        
        
        
        /** 
        * get_field_messages
        */

        protected function get_field_messages( $p = array() ) {

            $p += array(
                'info' => false,
                'info_html' => false
            );

            $ret = false;

            if ( $p['info'] ) {
                $ret .= '<span class="info">' . $p['info'] . '</span>';
            }

            if ( $p['info_html'] ) {
                $ret .= '<div class="info_html">' . $p['info_html'] . '</div>';
            }

            if ( isset( $this->validation[ $this->get_field_name( $p ) ] ) ) {
                $ret .= '<span class="error">' . implode( '<br/>', $this->validation[ $this->get_field_name( $p ) ] ) . '</span>';
            }

            if ( $ret ) {
                return '<div class="field-message">' . $ret . '</div>';
            }
            else {
                return $ret;
            }
        }
        
        
        
        /** 
        * get_field_sufix
        */

        protected function get_field_sufix( $p = array() ) {

            $p += array(
                'sufix' => false
            );

            $ret = false;

            if ( $p['sufix'] ) {
                $ret .= '<div class="field-sufix">';
                $ret .= '<span>' . $p['sufix'] . '</span>';
                $ret .= '</div>';
            }
            
            return $ret;
        }
        
    }
    
?>