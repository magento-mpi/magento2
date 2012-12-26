<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
$store = Mage::getModel('Mage_Core_Model_Store');
$store->setData(
    array(
        'name' => 'Test Store View' . uniqid(),
        'code' => 'store_' . uniqid(),
        'is_active' => true,
    )
);
return $store;
