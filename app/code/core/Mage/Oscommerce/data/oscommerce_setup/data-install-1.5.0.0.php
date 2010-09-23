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
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright   Copyright (c) 2010 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();

$data = array(
    array('type_code'=>'website',       'type_name'=>'Website'),
    array('type_code'=>'store',         'type_name'=>'Store'),
    array('type_code'=>'category',      'type_name'=>'Category'),
    array('type_code'=>'product',       'type_name'=>'Product'),
    array('type_code'=>'customer',      'type_name'=>'Customer'),
    array('type_code'=>'order',         'type_name'=>'Order'),
    array('type_code'=>'group',         'type_name'=>'Store Group'),
    array('type_code'=>'taxclass',      'type_name'=>'Product Tax Class'),
    array('type_code'=>'root_category', 'type_name'=>'Root Category')
);

$installer->getConnection()->insertMultiple($installer->getTable('oscommerce/oscommerce_type'), $data);

$installer->endSetup();