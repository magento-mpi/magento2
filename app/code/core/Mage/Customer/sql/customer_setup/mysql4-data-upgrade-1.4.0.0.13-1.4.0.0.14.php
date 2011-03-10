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
 * @package     Mage_Customer
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/* @var $installer Mage_Customer_Model_Entity_Setup */
$installer = $this;
/* @var $eavConfig Mage_Eav_Model_Config */
$eavConfig = Mage::getSingleton('eav/config');

// update customer system attributes used_in_forms data
$attributes = array(
    'confirmation', 'default_billing', 'default_shipping', 'password_hash', 'website_id', 'created_in', 'store_id',
    'group_id', 'prefix', 'firstname', 'middlename', 'lastname', 'suffix', 'email', 'dob', 'taxvat', 'gender'
);

$defaultUsedInForms = array(
    'customer_account_create',
    'customer_account_edit',
    'checkout_register',
);

foreach ($attributes as $attributeCode) {
    $attribute = $eavConfig->getAttribute('customer', $attributeCode);
    if (!$attribute) {
        continue;
    }
    if (false === ($attribute->getData('is_system') == 1 && $attribute->getData('is_visible') == 0)) {
        $usedInForms = $defaultUsedInForms;
        $adminHtmlOnly = $attribute->getData('adminhtml_only');
        $adminCheckout = $attribute->getData('admin_checkout');
        if (!empty($adminHtmlOnly)) {
            $usedInForms = array('adminhtml_customer');
        } else {
            $usedInForms[] = 'adminhtml_customer';
        }
        if (!empty($adminCheckout)) {
            $usedInForms[] = 'adminhtml_checkout';
        }
        $attribute->setData('used_in_forms', $usedInForms);
    }
    $attribute->save();
}

// update customer address system attributes used_in_forms data
$attributes = array(
    'prefix', 'firstname', 'middlename', 'lastname', 'suffix', 'company', 'street', 'city', 'country_id',
    'region', 'region_id', 'postcode', 'telephone', 'fax'
);

$nameAttributes = array(
    'prefix', 'firstname', 'middlename', 'lastname', 'suffix'
);

$defaultUsedInForms = array(
    'adminhtml_customer_address',
    'customer_address_edit',
    'customer_register_address'
);

foreach ($attributes as $attributeCode) {
    $attribute = $eavConfig->getAttribute('customer_address', $attributeCode);
    if (!$attribute) {
        continue;
    }
    if (false === ($attribute->getData('is_system') == 1 && $attribute->getData('is_visible') == 0)) {
        $attribute->setData('used_in_forms', $defaultUsedInForms);
    }
    $attribute->save();
}
