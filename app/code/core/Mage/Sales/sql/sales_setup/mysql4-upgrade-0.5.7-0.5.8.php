<?php

$this->addAttribute('quote', 'customer_tax_class_id', array(
	'type'=>'int',
	'label'=>'Customer Tax Class',
	'input'=>'select',
	'source'=>'tax/class_source_customer',
));

$this->addAttribute('quote_item', 'tax_class_id', array(
	'type'=>'int',
	'label'=>'Tax Class',
	'input'=>'select',
	'source'=>'tax/class_source_product',
));