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
 * @category   Mage
 * @package    Mage_Tests
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

if (!defined('_IS_INCLUDED')) {
    require dirname(__FILE__) . '/../PHPUnitTestInit.php';
    PHPUnitTestInit::runMe(__FILE__);
}

/**
 * WebServices Customer test case
 */
class WebService_CustomerTest extends WebService_TestCase_Abstract
{
    /**
     * customer.info - Retrieve customer data
     *
     * @dataProvider connectorProvider
     */
    public function testAll(WebService_Connector_Interface $connector)
    {
            // view all customers
            $this->assertTrue(is_array($connector->call('customer.list')), 'Failed to list customers.');

            // create new customer
            $customerName = __CLASS__ . uniqid(null, true);
            $newCustomer = array(
                'firstname'     => $customerName,
                'lastname'      => $customerName,
                'email'         => $customerName . '@example.com',
                'password_hash' => md5($customerName),
                'store_id'      => 0,
                'website_id'    => 0
            );
            $newCustomerId = (int)$connector->call('customer.create', array($newCustomer));
            $this->assertTrue($newCustomerId > 0, 'Failed to create new customer.');

            // get new customer info
            $newCustomerInfo = $connector->call('customer.info', $newCustomerId);
            $this->assertTrue(
                is_array($newCustomerInfo)
                && isset($newCustomerInfo['customer_id'])
                && isset($newCustomerInfo['created_at'])
                && isset($newCustomerInfo['updated_at'])
                && isset($newCustomerInfo['increment_id'])
                && isset($newCustomerInfo['store_id'])
                && isset($newCustomerInfo['website_id'])
                && isset($newCustomerInfo['created_in'])
                && isset($newCustomerInfo['email'])
                && isset($newCustomerInfo['firstname'])
                && isset($newCustomerInfo['group_id'])
                && isset($newCustomerInfo['lastname'])
                && isset($newCustomerInfo['password_hash'])
                , 'New created customer data does not contain required keys.'
            );
            $this->assertEquals($customerName, $newCustomerInfo['firstname'], 'New created customer has wrong name.');


            // update customer - change name
            $customerName .= 'changed';
            $connector->call('customer.update', array($newCustomerId, array('firstname' => $customerName)));
            $updatedInfo = $connector->call('customer.info', $newCustomerId);
            $this->assertEquals($updatedInfo['firstname'], $customerName, 'Failed to update customer name.');

            // delete customer
            $connector->call('customer.delete', $newCustomerId);
    }
}
