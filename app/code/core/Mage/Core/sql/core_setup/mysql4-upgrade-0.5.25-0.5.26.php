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