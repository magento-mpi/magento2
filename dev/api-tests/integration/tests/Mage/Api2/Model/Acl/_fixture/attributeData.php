<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Api2
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Attribute data fixture
 */
/** @var $guest Mage_Api2_Model_Auth_User_Guest */
$guest = Mage::getModel('api2/auth_user_guest');

/** @var $customer Mage_Api2_Model_Auth_User_Customer */
$customer = Mage::getModel('api2/auth_user_customer');

return array(
    'create' => array(
        'user_type' => $guest->getType(),
        'resource_id' => 'test/integration/resource_id',
        'operation'    => Mage_Api2_Model_Resource::OPERATION_RETRIEVE,
        'allowed_attributes' => 'title,description,short_description'
    ),
    'update' => array(
        'user_type' => $customer->getType(),
        'resource_id' => 'test/integration/resource_id/update',
        'operation'    => Mage_Api2_Model_Resource::OPERATION_UPDATE,
        'allowed_attributes' => 'title,description'
    )
);
