<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
$website = Mage::getModel('Mage_Core_Model_Website');
$website->setData(
    array(
        'code' => 'test_' . uniqid(),
        'name' => 'Test Website' . uniqid(),
        'default_group_id' => 1,
    )
);
return $website;
