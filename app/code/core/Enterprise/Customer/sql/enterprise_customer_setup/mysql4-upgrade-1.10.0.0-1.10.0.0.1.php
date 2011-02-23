<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
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
 * @category    Enterprise
 * @package     Enterprise_Customer
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

$installer = $this;

/* @var $installer Mage_Eav_Model_Entity_Setup */
$installer->startSetup();

/** @var $helper Enterprise_Customer_Helper_Data */
$helper = Mage::helper('enterprise_customer');
$customerAttributes = $helper->getCustomerAttributeFormOptions();

$attributes = array(
    array(
        'attribute_code'    => 'dob'
    ),
    array(
        'attribute_code'    => 'gender'
    ),
    array(
        'attribute_code'    => 'taxvat'
    ),
);

foreach ($attributes as &$value) {
    $attribute = $installer->getAttribute('customer', $value['attribute_code']);
    $value['attribute_id'] = $attribute['attribute_id'];
}

foreach ($attributes as $attribute) {
    foreach($customerAttributes as $customerAttribute) {
        $installer->run('
            INSERT INTO '.$installer->getTable('customer/form_attribute').' (form_code, attribute_id)
                VALUES ("'.$customerAttribute['value'].'", '.$attribute['attribute_id'].')
                ON DUPLICATE KEY UPDATE form_code = form_code;
        ');
    }
}
$installer->endSetup();
