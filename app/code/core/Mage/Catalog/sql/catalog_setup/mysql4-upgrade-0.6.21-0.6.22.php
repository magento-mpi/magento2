<?php

$this->addAttribute('catalog_category', 'url_key', array(
	'label' => 'SEF URL Identifier<br/>(will replace category name)',
	'required' => false,
));

$this->addAttribute('catalog_product', 'small_image', array(
	'backend'=>'catalog_entity/product_attribute_backend_image',
	'frontend'=>'catalog_entity/product_attribute_frontend_image',
	'input'=>'image',
	'label'=>'Small Image',
));

$this->run("delete from core_config_field where path='catalog/frontend/product_per_page'");