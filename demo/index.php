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

						$form->text['en-US']['fields']['email']['label'] = 'E-Mail';
						$form->text['en-US']['fields']['select']['label'] = 'Select';
						$form->text['en-US']['fields']['checkbox']['label'] = 'Checkbox';
						$form->text['en-US']['fields']['radio']['label'] = 'Radio';
						$form->text['en-US']['fields']['message']['label'] = 'Message';

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
						'mail_send_script' => 'swift',
						'mail_send_methode' => 'phpmail', // phpmail, sendmail, smtp
						'mail_send_config' => array(
							'sendmail' => array(
								'path' => '/usr/sbin/sendmail -t -i -f'
							),
							'smtp' => array(
								'server' => false,
								'port' => false,
								'user' => false,
								'password' => false,
								'ssl' => false
							)
						),
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
						'group-layout' => 'block',
						'list-layout' => 'table',
					));

						/* fields and field groups */

						$form->set( 'field_text', array(
							'name' => 'email',
							'sufix' => 'Sufix',
							'prefix' => 'Prefix',
							'validation' => array(),
						));

						$form->set( 'field_select', array(
						    'name' => 'select',
						    'array' => false,
						    'data' => array(
					        	'' => 'wähle…',
					        	'a' => 'A',
								'b' => 'B',
								'Numbers' => array(
									'1' => '1',
									'2' => '2',
								)
						    ),
						    'validation' => array()
						));

						$form->set( 'field_checkbox', array(
						    'name' => 'checkbox',
						    'array' => false,
						    'labeltype' => 'field-after',
						    'data' => 'checked',
							'checked' => true,
							'validation' => array(),
						));

						$form->set( 'field_radio', array(
						    'name' => 'radio',
						    'array' => false,
						    'labeltype' => 'field-after',
						    'data' => 'checked',
						    'checked' => true,
							'validation' => array(),
						));

						$form->set( 'field_textarea', array(
							'name' => 'message',
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
