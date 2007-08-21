<?php

$this->addConfigField('catalog/product', 'Product options');
$this->addConfigField('catalog/product/default_tax_group', 'Default tax class', array(
	'frontend_type'=>'select',
	'source_model'=>'tax/class_source_product',
));