<?php

$conn->query("delete from core_config_data where path like 'catalog%'");
$this->addConfigField('catalog', 'Catalog', array());
$this->addConfigField('catalog/category', 'Category', array('show_in_default'=>false,'show_in_website'=>false));
$this->addConfigField('catalog/category/root_id', 'Root category', array('show_in_default'=>false,'show_in_website'=>false));
$this->addConfigField('catalog/frontend', 'Frontend', array());
$this->addConfigField('catalog/frontend/product_per_page', 'Product per page', array());
$this->addConfigField('catalog/images', 'Images Configuration', array());
$this->addConfigField('catalog/images/category_upload_path', 'Category upload directory', array(), '{{root_dir}}/media/catalog/category/');
$this->addConfigField('catalog/images/category_upload_url', 'Category upload url', array(), '{{base_path}}media/catalog/category/');
$this->addConfigField('catalog/images/product_upload_path', 'Product upload directory', array(), '{{root_dir}}/media/catalog/product/');
$this->addConfigField('catalog/images/product_upload_url', 'Product upload url', array(), '{{base_path}}media/catalog/product/');