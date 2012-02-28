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
 * @package     Magento_Test
 * @subpackage  integration_tests
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magento.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Test for customer address API2 (customer)
 *
 * @category    Magento
 * @package     Magento_Test
 * @author      Magento Api Team <api-team@magento.com>
 */
class Api2_Customer_Address_CustomerTest extends Magento_Test_Webservice_Rest_Customer
{
    /**
     * Delete fixtures
     */
    protected function tearDown()
    {
        Magento_Test_Webservice::deleteFixture('customer', true);

        $fixtureAddresses = $this->getFixture('addresses');
        if ($fixtureAddresses && count($fixtureAddresses)) {
            foreach ($fixtureAddresses as $fixtureAddress) {
                $this->callModelDelete($fixtureAddress, true);
            }
        }

        parent::tearDown();
    }

    /**
     * Test get customer addresses for customer
     * @magentoDataFixture Api2/Customer/_fixtures/add_addresses_to_current_customer.php
     */
    public function testGetCustomerAddress()
    {
        /* @var $customer Mage_Customer_Model_Customer */
        $customer = Mage::getModel('customer/customer');
        $customer->setWebsiteId(Mage::app()->getWebsite()->getId())->loadByEmail(TESTS_CUSTOMER_EMAIL);
        /* @var $fixtureCustomerAddress Mage_Customer_Model_Address */
        $fixtureCustomerAddress = $customer
            ->getAddressesCollection()
            ->getFirstItem();
        $restResponse = $this->callGet('customers/addresses/' . $fixtureCustomerAddress->getId());
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $restResponse->getStatus());

        $responseData = $restResponse->getBody();
        $this->assertNotEmpty($responseData);

        foreach ($responseData as $field => $value) {
            $this->assertEquals($value, $fixtureCustomerAddress->getData($field));
        }
    }

    /**
     * Test retrieving addresses for not existing customer
     */
    public function testGetUnavailableCustomerAddress()
    {
        $restResponse = $this->callGet('customers/invalid_id/addresses');
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_NOT_FOUND, $restResponse->getStatus());
    }

    /**
     * Test get order items if customer is not owner
     *
     * @magentoDataFixture Api2/Customer/_fixtures/customer_with_addresses.php
     */
    public function testGetCustomerAddressIfCustomerIsNotOwner()
    {
        /* @var $fixtureCustomerAddress Mage_Customer_Model_Address */
        $fixtureCustomerAddress = $this->getFixture('customer')
            ->getAddressesCollection()
            ->getFirstItem();
        $restResponse = $this->callGet('customers/addresses/' . $fixtureCustomerAddress->getId());
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_NOT_FOUND, $restResponse->getStatus());
    }
}
