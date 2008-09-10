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
    require dirname(__FILE__) . '/../../PHPUnitTestInit.php';
    PHPUnitTestInit::runMe(__FILE__);
}

class WebService_Customer_AddressTest extends WebService_TestCase_Abstract
{

    /**
     * @dataProvider connectorProvider
     */
    public function testList(WebService_Connector_Interface $connector)
    {
        $newCustomer = array(
            'firstname'  => 'First',
            'lastname'   => 'Last',
            'email'      => 'test_'.uniqid().'@example.com',
            'password'   => 'password',
            'store_id'   => 0,
            'website_id' => 0
        );
        $newCustomerId = $connector->call('customer.create', array($newCustomer));
        $newCustomerAddress = array(
            'firstname'  => 'First',
            'lastname'   => 'Last',
            'country_id' => 'USA',
            'region_id'  => '43',
            'region'     => 'New York',
            'city'       => 'New York',
            'street'     => array('Bla bla','bla bla'),
            'telephone'  => '5555-555',
            'postcode'   => 10021,

            'is_default_billing'  => true,
            'is_default_shipping' => true
        );
        $newCustomerAddressId = $connector->call('customer_address.create', array($newCustomerId, $newCustomerAddress));
        $addressList = $connector->call('customer_address.list', $newCustomerId);

        $addressFirstname = 'Changedfirst';
        $customerAddressUpdate = array(
            'firstname' => $addressFirstname
        );
        $connector->call('customer_address.update', array($newCustomerAddressId, $customerAddressUpdate));
        $updatedAddressList = $connector->call('customer_address.list', $newCustomerId);

        $customerInfo = $connector->call('customer_address.info', $newCustomerAddressId);

        $connector->call('customer_address.delete', $newCustomerAddressId);
        $connector->call('customer.delete', $newCustomerId);

        $this->assertType('array', $addressList);
        $this->assertTrue(isset($addressList[0]));
        $this->assertTrue($addressList[0]['customer_id'] == $newCustomerId);
        $this->assertTrue($updatedAddressList[0]['firstname'] == $addressFirstname);
        $this->assertTrue(isset($customerInfo['customer_id']));
        $this->assertTrue($customerInfo['customer_id'] == $newCustomerId);
    }
}