<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Customer
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Block\Account\Dashboard;

class AddressTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Customer\Block\Account\Dashboard\Address
     */
    protected $_block;

    /** @var  \Magento\Customer\Model\Session */
    protected $_customerSession;

    protected function setUp()
    {
        $this->_customerSession = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->get('\Magento\Customer\Model\Session');
        $this->_block = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\View\LayoutInterface')
            ->createBlock(
                'Magento\Customer\Block\Account\Dashboard\Address',
                '',
                array('customerSession' => $this->_customerSession)
            );
    }

    protected function tearDown()
    {
        $this->_customerSession->unsCustomerId();
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testGetCustomer()
    {
        $customer = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Customer\Service\V1\CustomerServiceInterface')->getCustomer(1);

        $this->_customerSession->setCustomerId(1);
        $object = $this->_block->getCustomer();
        $this->assertEquals($customer, $object);
    }

    public function testGetCustomerMissingCustomer()
    {
        $this->assertNull($this->_block->getCustomer());
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoDataFixture Magento/Customer/_files/customer_two_addresses.php
     */
    public function testGetPrimaryShippingAddressHtml()
    {
        $expected = "John Smith<br/>\n\nGreen str, 67<br />\n\n\n\nCityM,  Alabama, 75477<br/>\n<br/>\nT: 3468676\n\n";
        $this->_customerSession->setCustomerId(1);
        $html = $this->_block->getPrimaryShippingAddressHtml();
        $this->assertEquals($expected, $html);
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testGetPrimaryShippingAddressHtmlNoAddress()
    {
        $expected = 'You have not set a default shipping address.';
        $this->_customerSession->setCustomerId(1);
        $html = $this->_block->getPrimaryShippingAddressHtml();
        $this->assertEquals($expected, $html);
    }

    public function testGetPrimaryShippingAddressHtmlMissingCustomer()
    {
        $expected = 'You have not set a default shipping address.';

        $html = $this->_block->getPrimaryShippingAddressHtml();
        $this->assertEquals($expected, $html);
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoDataFixture Magento/Customer/_files/customer_two_addresses.php
     */
    public function testGetPrimaryBillingingAddressHtml()
    {
        $expected = "John Smith<br/>\n\nGreen str, 67<br />\n\n\n\nCityM,  Alabama, 75477<br/>\n<br/>\nT: 3468676\n\n";
        $this->_customerSession->setCustomerId(1);
        $html = $this->_block->getPrimaryBillingAddressHtml();
        $this->assertEquals($expected, $html);
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testGetPrimaryBillingAddressHtmlNoAddress()
    {
        $expected = 'You have not set a default billing address.';
        $this->_customerSession->setCustomerId(1);
        $html = $this->_block->getPrimaryBillingAddressHtml();
        $this->assertEquals($expected, $html);
    }

    public function testGetPrimaryBillingAddressHtmlMissingCustomer()
    {
        $expected = 'You have not set a default billing address.';

        $html = $this->_block->getPrimaryBillingAddressHtml();
        $this->assertEquals($expected, $html);
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoDataFixture Magento/Customer/_files/customer_two_addresses.php
     */
    public function testGetPrimaryShippingAddressEditUrl()
    {
        $expected = 'http://localhost/index.php/customer/address/edit/id/1/';
        $this->_customerSession->setCustomerId(1);
        $url = $this->_block->getPrimaryShippingAddressEditUrl();
        $this->assertEquals($expected, $url);
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer_no_address.php
     */
    public function testGetPrimaryShippingAddressEditUrlNoAddress()
    {
        // set up
        $this->_customerSession->setCustomerId(5);

        // verify
        $url = $this->_block->getPrimaryShippingAddressEditUrl();
        $expected = 'http://localhost/index.php/customer/address/edit/';
        $this->assertEquals($expected, $url);
    }

    public function testGetPrimaryShippingAddressEditUrlMissingCustomer()
    {
        $url = $this->_block->getPrimaryShippingAddressEditUrl();
        $this->assertEquals('', $url);
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoDataFixture Magento/Customer/_files/customer_two_addresses.php
     */
    public function testGetPrimaryBillingAddressEditUrl()
    {
        $expected = 'http://localhost/index.php/customer/address/edit/id/1/';
        $this->_customerSession->setCustomerId(1);
        $url = $this->_block->getPrimaryBillingAddressEditUrl();
        $this->assertEquals($expected, $url);
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer_no_address.php
     */
    public function testGetPrimaryBillingAddressEditUrlNoAddress()
    {
        // set up
        $this->_customerSession->setCustomerId(5);

        // verify
        $url = $this->_block->getPrimaryBillingAddressEditUrl();
        $expected = 'http://localhost/index.php/customer/address/edit/';
        $this->assertEquals($expected, $url);
    }

    public function testGetPrimaryBillingAddressEditUrlMissingCustomer()
    {
        $url = $this->_block->getPrimaryBillingAddressEditUrl();
        $this->assertEquals('', $url);
    }
}
