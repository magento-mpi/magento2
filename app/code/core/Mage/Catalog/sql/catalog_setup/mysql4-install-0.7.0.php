<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Core
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

$this->addConfigField('catalog', 'Catalog', array(
	'frontend_type'=>'text',
	'sort_order'=>'40',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '');
$this->addConfigField('catalog/category', 'Category', array(
	'frontend_type'=>'text',
	'sort_order'=>'1',
	'show_in_default'=>'0',
	'show_in_website'=>'0',
	'show_in_store'=>'1',
	), '');
$this->addConfigField('catalog/category/root_id', 'Root category', array(
	'frontend_type'=>'select',
	'frontend_class'=>'required-entry',
	'backend_model'=>'adminhtml/system_config_backend_category',
	'source_model'=>'adminhtml/system_config_source_category',
	'sort_order'=>'1',
	'show_in_default'=>'0',
	'show_in_website'=>'0',
	'show_in_store'=>'1',
	), '3');
$this->addConfigField('catalog/frontend', 'Frontend', array(
	'frontend_type'=>'text',
	'sort_order'=>'2',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '');
$this->addConfigField('catalog/frontend/list_mode', 'List Mode', array(
	'frontend_type'=>'select',
	'source_model'=>'adminhtml/system_config_source_catalog_listMode',
	'sort_order'=>'1',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), 'grid-list');
$this->addConfigField('catalog/frontend/list_mode', 'List Mode', array(
	'frontend_type'=>'select',
	'source_model'=>'adminhtml/system_config_source_catalog_listMode',
	'sort_order'=>'1',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), 'grid-list');
$this->addConfigField('catalog/images', 'Images Configuration', array(
	'frontend_type'=>'text',
	'sort_order'=>'3',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '');
$this->addConfigField('catalog/images/category_upload_path', 'Category upload directory', array(
	'frontend_type'=>'text',
	'sort_order'=>'1',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '{{root_dir}}/media/catalog/category/');
$this->addConfigField('catalog/images/category_upload_path', 'Category upload directory', array(
	'frontend_type'=>'text',
	'sort_order'=>'1',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '{{root_dir}}/media/catalog/category/');
$this->addConfigField('catalog/images/category_upload_url', 'Category upload url', array(
	'frontend_type'=>'text',
	'sort_order'=>'2',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '/media/catalog/category/');
$this->addConfigField('catalog/images/category_upload_url', 'Category upload url', array(
	'frontend_type'=>'text',
	'sort_order'=>'2',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '/media/catalog/category/');
$this->addConfigField('catalog/images/product_upload_path', 'Product upload directory', array(
	'frontend_type'=>'text',
	'sort_order'=>'3',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '{{root_dir}}/media/catalog/product/');
$this->addConfigField('catalog/images/product_upload_path', 'Product upload directory', array(
	'frontend_type'=>'text',
	'sort_order'=>'3',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '{{root_dir}}/media/catalog/product/');
$this->addConfigField('catalog/images/product_upload_url', 'Product upload url', array(
	'frontend_type'=>'text',
	'sort_order'=>'4',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '/media/catalog/product/');
$this->addConfigField('catalog/images/product_upload_url', 'Product upload url', array(
	'frontend_type'=>'text',
	'sort_order'=>'4',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '/media/catalog/product/');
$this->addConfigField('catalog/product', 'Product options', array(
	'frontend_type'=>'text',
	'sort_order'=>'5',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '');
$this->addConfigField('catalog/product/default_tax_group', 'Default tax class', array(
	'frontend_type'=>'select',
	'source_model'=>'tax/class_source_product',
	'sort_order'=>'1',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '2');
$this->addConfigField('catalog/product/default_tax_group', 'Default tax class', array(
	'frontend_type'=>'select',
	'source_model'=>'tax/class_source_product',
	'sort_order'=>'1',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '2');

$this->addConfigField('sendfriend', 'Email to a Friend', array(
	'frontend_type'=>'text',
	'sort_order'=>'31',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '');
$this->addConfigField('sendfriend/email', 'Email templates', array(
	'frontend_type'=>'text',
	'sort_order'=>'1',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '');
$this->addConfigField('sendfriend/email/template', 'Select email template', array(
	'frontend_type'=>'select',
	'source_model'=>'catalog/sendToFriend',
	'sort_order'=>'1',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '10');