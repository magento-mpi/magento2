<?php
$this->addAttribute('customer', 'created_in', array(
	'type'=>'int',
	'label'=>'Created From',
	'input'=>'select',
	'source'=>'customer_entity/customer_attribute_source_store',
));
$this->addAttribute('customer', 'store_id', array(
	'type'=>'static',
	'label'=>'Create In',
	'input'=>'select',
	'source'=>'customer_entity/customer_attribute_source_store',
	'backend'=>'customer_entity/customer_attribute_backend_store',
));
