<?php

$this->addAttribute('catalog_product', 'tax_class_id', array(
	'type'=>'int', 
	'input'=>'select',
	'label'=>'Tax Class', 
	'source'=>'tax/class_source_product', 
	'required'=>true,
));