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
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Magento
 * @package     Mage_Api2
 * @subpackage  integration_tests
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magento.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
