YerForm
====================

is a PHP class to create forms and handle serverside-validations. The result can be send via e-mail or a function can be defined that will fire on requesting data having the formdata in the parameter.

Building forms can be horrible, they are allways different and webdesigners are talented in making it nearly impossible. This piece of code tries to put the beast in a cage.

This documentation is incomplete and corrupting changes still may ahead!

Roadmap
--------------------

Just ask me for your needs at mail@johannheyne.de or create a new issue.

* captcha
* complete documentation
* definable dateformat for date validation
* config swift (PHP Mail, Sendmail, Smtp & SSL)
* [progressive] basic css styling, focusing on layoutoptions

### Changelog

* 02.01.2014
  Added multi language support.
  Now you can define multiple translations for all textsections like messages, validations, labels etc.

Setup
--------------------

###	Loading the YerForm Class

Autoload the "core/yerform.php". The class will only be loaded if it is needed.

```php
function __autoload( $class_name ) {
	if ( $class_name == 'YerForm' ) {
		require_once( 'yerform/core/yerform.php');
	}
}
```

###	Using YerForm Themes

Copy the styles of a YerForm Theme into your own styles an modify them as you need. Include a separate YerForm stylesheet only if the YerForm class exists.

```php
if ( class_exists( 'YerForm' ) ) {
	echo '<link rel="stylesheet" type="text/css" href="my-yerform-styles.css" />';
}
```

###	Create a New Form

```php
$form = new YerForm();
$form->form_id = 'my_contact_form';
```

###	Configuration

```php
$form->config( array(
	'form_class' => false,
	'action' => false,
	'sent_page' => false,
	'honeypot' => 'Honeypot',
	'language' => 'en-US',
	'call_function_on_validation_is_true' => false,
	'mail_form' => false,
	'mail_send_script' => 'swift',
	'mail_send_methode' => 'phpmail', // phpmail, sendmail, smtp
	'mail_send_config' => array(
		'sendmail' => array(
			'path' => false
		),
		'smtp' => array(
			'server' => false,
			'port' => false,
			'user' => false,
			'password' => false,
			'ssl' => false
		)
	),
	'mail_subject' => 'Contact',
	'field_sender_mail' => 'email',
	'fields_sender_name' => array( 'surename', 'familyname' ),
	'recipient_mail' => 'my email',
	'recipient_name' => 'my name',
	'mail_text' => "
		Contactform
		Name: {surename} {familyname}
		E-Mail: {email}
		Message:
		{message}
	",
	'message_error_main' => array(
		'typ'=>'error',
		'text'=>'Could not send form! Check the following fields: {fields}'
	),
	'message_sending' => array(
		'typ'=>'info',
		'text'=>'Sending!'
	),
	'message_sent' => array(
		'typ'=>'info',
		'text'=>'Sent!'
	),
	'messages_validation' => array(
		'required' => array( 'text'=>'required' ),
		'email' => array( 'text'=>'invalid' )
	),
	'message_checkdate' => 'date does not exists',
	'message_dateformat' => 'please format the date like 01.06.2013'
));
```

**sent_page**  
*(string)*  
URL to redirect, after a form was sent.
The *default* is false.

**language**  
*(language code)*  
Defines the language for all forms on the page.
This is used by the jQuery.datepicker!

###	Multi Language Setup

In this <code>$form->text</code> array, the translations for languages can be managed. The current language refers to the configuration in <code>$form->config( array( 'language' => 'en-US' ) );</code>.

```php
$form->text = array(
	'en-US' => array(
		'message_error_main' => array(
			'typ'=>'error',
			'text'=>'Could not send form! Check the following fields: {fields}',
		),
		'message_sending' => array( 
			'typ'=>'info',
			'text'=>'Sending!',
		),
		'message_sent' => array(
			'typ'=>'info',
			'text'=>'Sent!',
		),
		'message_checkdate' => 'date does not exists',
		'message_dateformat' => 'please format the date like 01.06.2013',
		'messages_validation' => array(
			'required' => array( 'text' => 'required' ),
			'email' => array( 'text' => 'invalid' ),
		),
		'fieldset' => array(
			'require_info' => array(
				'text' => 'Fields marked with {require_symbol} are required.',
			),
		),
		'fieldsets' => array(
			'contactdata' => array(
				'legend' => 'Contaktdata',
			),
			'require_info' => array(
				'text' => 'Fields marked with {require_symbol} are required.',
			),
		),
		'fields' => array(
			'email' => array(
				'label' => 'E-Mail',
			),
			'message' => array(
				'label' => 'Massage',
			),
		),
		'buttons' => array(
			'submit' => array(
				'label' => 'Submit',
			),
			'reset' => array(
				'label' => 'Cancel',
			),
		),
	),
);
```

Global Formular Messages
--------------------

This is for all global messages like validation and send status.

```php
$form->set( 'messages' );
```

Building the Form Structure
--------------------

The basic structure are fieldsets. Inside a fieldset belongs a list. Inside a list belongs fields and fieldgroups. A field represents a single list-item. But you can use groups for nesting the list.

### Fieldset and List

```php
$form->set( 'fieldset_begin', array(
	'name'=>'contactdata',
	'legend'=>'Contactdata',
	'class'=>false,
	'require_info'=> array(
		'text' => 'Fields marked with {require_symbol} are reqired.'
	)
));
	$form->set( 'list_begin', array(
		'list-layout' => 'block'
	));

		/* fields and field groups */

	$form->set( 'list_end' );
$form->set( 'fieldset_end' );
```

The placeholder <code>{require_symbol}</code> will show <span class="required">*</span> per default. You can change this via <code>$form->required_label_sufix = '…';</code>.

**list-layout**  
*(string) block, inline, table*

### Fieldgroups

```php
$form->set( 'group_begin', array(
	'label' => false,
	'group-layout' => 'block',
	'list-layout' => 'block',
	'list-gap' => false
));

	/* some fields */

$form->set( 'group_end');
```

**label**  
*(string)*  
Labelname of the group.

**group-layout**  
*(string) block, inline*

**list-layout**  
*(string) block, inline, table*

**list-gap**  
*(boolean)*  
Enables gap between fields or groups if list-layout is block or inline.

Fields
--------------------

### Text

```php
$form->set( 'field_text', array(
	'name' => 'no name',
	'array' => false,
	'label' => 'no name',
	'placeholder' => false,
	'sufix' => '',
	'value' => '',
	'size' => '40',
	'maxlength' => '200',
	'padding' => array( 0, 0 ),
	'class' => false,
	'validation' => false
));
```

**name**  
*(string)*  
The name and Id of the field.

**array**  
*(integer)*  
For fields with same name like a checkboxset.

**label**  
*(string)*  
The label of the field.

**placeholder**  
*(string)*  
Defines a placeholder text for the field.

**sufix**  
*(string)*  
Displaying after the Field.

**value**  
*(string)*  
Defines a value prefilled.

**size**  
*(integer)*  
Defines the size of the textfield. 
The *default* is $form->field_text_size = 40.

**maxlength**  
*(integer)*  
Defines the maximal lenght of the textfields content. 
The *default* is $form->field_text_maxlength = 200.

**padding**  
*(array( integer-left, integer-right ))*  
Defines the left and right padding of the field list-item. 
This is usefull for giving horizontal orientated fields some spacing.

**class**  
*(string)*  
Defines a class on the field list-item.

### Textarea

```php
$form->set( 'field_textarea', array(
	'name' => 'noname',
	'array' => false,
	'label' => 'no name',
	'value' => '',
	'cols' => $this->field_textarea_cols,
	'rows' => $this->field_textarea_rows,
	'padding' => array(0,0),
	'validation' => false
));
```

### Checkbox

```php
$form->set( 'field_checkbox', array(
	'name' => 'noname',
	'array' => false,
	'label' => 'no name',
	'labeltype' => 'field-after',
	'data' => 'checked',
	'validation' => false
));
```

### Radio

```php
$form->set( 'field_radio', array(
	'name' => 'noname',
	'array' => false,
	'label' => 'no name',
	'labeltype' => 'field-after',
	'data' => 'checked',
	'validation' => false
));
```

### Select

```php
$form->set( 'field_select', array(
	'name' => 'noname',
	'array' => false,
	'label' => 'no name',
	'data' => array(
		'' => 'wähle…'
	),
	'validation' => false
));
```

### Date

```php
$form->set( 'field_date', array(
	'label' => 'no name', 
	'name' => 'noname',
	'use_field_type' => 'date',
	'array' => false,
	'size' => false,
	'maxlength' => $form->field_text_maxlength,
	'padding' => array(0,0),
	'layout' => false,
	'returnformat' => 'd.m.Y',
	'datepicker' => false,
	'datepicker-mindate' => 0,
	'datepicker-maxdate' => 0,
	'datepicker-dateformat' => 'dd.mm.yy',
	'datepicker-iconurl' => false,
	'validation' => false
));

// returns
Array
(
	[request] => Array
		(
			[name] => the date in the format you set in the 'returnformat' parameter
			[name.timestamp] => 1397865600
		)
)
```

**label**  
*(string)*  
The label of the field.

**name**  
*(string)*  
The name and Id of the field.

**use_field_type**  
*(string)*  
Define the type of the field that can be "date" or "text".

**array**  
*(integer)*  
For fields with same name.

**size**  
*(integer)*  
Defines the size of the field. 
The *default* is $form->field_text_size = 40.

**maxlength**  
*(integer)*  
Defines the maximal lenght of the fields content. 
The *default* is ```$form->field_text_maxlength = 200```.

**padding**  
*( array( integer-left, integer-right ) )*  
Defines the left and right padding of the field list-item. 
This is usefull for giving horizontal orientated fields some spacing.

**returnformat**  
*(string)*  
Define the format of the date that will be return in the result.
Format the date like: http://www.php.net/manual/de/function.date.php

**datepicker**  
*(boolean)*  
Enables the jQuery-UI-Datepicker. The Datepicker is disabled for iOS devices because of their nativ date selection UI.
The Datepicker needs some extra files to included in the header:
jQuery, jQuery-UI-Core, jQuery-UI-Datepicker, jQuery-UI-Theme and may one or more datepicker regional translation scripts (https://github.com/jquery/jquery-ui/tree/master/ui/i18n)
* /jquery-ui/… place a jQuery-UI-Theme here an load the stylesheet in the header.
* /js/yerform.js
* /js/jquery.ui.datepicker-{language code}.js

**datepicker-mindate**  
*(0 or string)*

**datepicker-maxdate**  
*(0 or string)*

**datepicker-dateformat**  
*(string)*

**datepicker-iconurl**  
*(url)*

### Hidden

```php
$form->set( 'field_hidden', array(
	'name' => 'noname',
	'value' => ''
));
```

### HTML

```php
$form->set( 'field_html', array(
	'padding' => array(0,0),
	'content' => ''
));
```

**content**  
*(string)*  
HTML-Code to display.

Validation
--------------------

Just a sample, where to place the validation array:

```php
$form->set( 'field_text', array(
	'name' => 'surename',
	'label' => 'Surename',
	'layout' => 'table',
	'validation' => array(
		0 => array(
			'type' => 'required',
			'cond' => true,
			'message' => $messages['required']
		)
	)
));```

Just put the messages for each validation error type in a variable as an array like this:
$messages['required'];

### Required

```php
array(
	'type' => 'required',
	'cond' => true,
	'message' => $messages['required']
)
```

### E-Mail

```php
array(
	'type' => 'email',
	'message' => $messages['email']
)
```

### Date

```php
array(
	'type' => 'date',
	'min' => '+1 day',
	'max' => '+2 year',
	'dependency' => array(
		'field' => 'fieldname',
		'value' => '+1 day',
		'operator' => '>=',
		'message' => $messages['date-dependency']
	),
	'message-checkdate' => $messages['checkdate'],
	'message-dateformat' => $messages['dateformat'],
	'message-min-max' => $messages['date-min-max']
)
```

**min max**  
*(You can use the same all definitions of http://de1.php.net/manual/en/datetime.formats.php)*  
Defines the minimum and maximum of the date.
For example:
'now'
'05-07-2012'
'05-07-2012 -1 day'
'10 September 2000'
'+1 day'
'+1 week'
'-1 year +2 days'
'next Thursday'
'last Monday'

**dependency**  
Depending the value with the value of another date-field.

**checkdate**  
If a date not exists for example 32.13.2012

**message-dateformat**  
If the date does not fit the format "dd.mm.yy"
The date format will be changeable in futur.

**message-min-max**  
If the date does not fit the given range of min and/or max.

Output The Form
--------------------

```php
$form->run( array(
	'output' => 'return'
));
```

**output**  
*('echo' | 'return')*  
Defines the type of output.

Submit And Reset Button
--------------------

```php
$form->set( 'form_buttons', array(
	'submit' => true,
	'submit_label' => 'Submit',
	'submit_class' => false,
	'submit_btn_class' => false,
	'reset' => true,
	'reset_label' => 'Reset',
	'reset_class' => false,
	'reset_btn_class' => false
));
```

**submit**  
*(false | true)*  
Enables the submit-button.

**submit_label**  
*(string)*  
Defines the buttontext.

**submit_class**  
*(false | string)*  
Defines the class of the submit-button container.

**submit_btn_class**  
*(false | string)*  
Defines the class of the submit-button itself.

**reset**  
*(false | true)*  
Enables the reset-button.

**reset_label**  
*(string)*  
Defines the buttontext.

**reset_class**  
*(false | string)*  
Defines the class of the reset-button container.

**reset_btn_class**  
*(false | string)*  
Defines the class of the reset-button itself.

Ajax
--------------------

yerform is using a html redirect after sending the form to prevent resending the form by pagereload. This wont work, if the form is part of an ajax request. You can prevent this by adding the parameter ajax=y to the action url.

```php
jQuery('body').find('.formwrap form').submit( function(event) {
	event.preventDefault();

	var form = jQuery(this),
	url = form.attr('action');

	jQuery.post( url + '?ajax=y', form.serialize(), function( data ) {

		jQuery('.formwrap').html( data );

	});
});
```