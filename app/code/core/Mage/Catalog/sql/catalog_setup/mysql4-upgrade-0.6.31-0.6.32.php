<?php

$this->addConfigField('sendfriend', 'Email to a Friend', 
	array(
    'frontend_type'=>'text',
)); 

$this->addConfigField('sendfriend/emTemplates', 'Email templates', 
	array(
    'frontend_type'=>'text',
)); 

$this->addConfigField('sendfriend/emTemplates/template', 'Select email template', 
	array(
    'frontend_type'=>'select',
    'source_model'=>'catalog/sendToFriend',
)); 