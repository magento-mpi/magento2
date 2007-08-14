<?php

$this->addAttribute('quote_address_item', 'tax_class_id', array(
	'type'=>'int',
	'label'=>'Tax Class',
	'input'=>'select',
	'source'=>'tax/class_source_product',
));