<?php

$this->addAttribute('catalog_category', 'url_key', array(
	'label'=>'URL key',
	'backend'=>'catalog_entity/category_attribute_backend_urlkey',
));
$this->addAttribute('catalog_product', 'url_key', array(
	'label'=>'URL key',
	'backend'=>'catalog_entity/product_attribute_backend_urlkey',
));
