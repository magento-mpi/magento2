<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
$storeGroup = Mage::getModel('Mage_Core_Model_Store_Group');
$storeGroup->setData(
    array(
        'name' => 'Test Store' . uniqid(),
        'code' => 'store_group_' . uniqid(),
    )
);
return $storeGroup;
