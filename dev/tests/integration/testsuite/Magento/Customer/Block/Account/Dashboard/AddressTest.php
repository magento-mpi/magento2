<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Block\Account\Dashboard;

use Magento\Customer\Service\V1\CustomerAccountServiceInterface;
use Magento\TestFramework\Helper\Bootstrap;

class AddressTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Customer\Block\Account\Dashboard\Address
     */
    protected $_block;

    /** @var  \Magento\Customer\Model\Session */
    protected $_customerSession;

    /**
     * @var \Magento\Module\Manager
     */
    protected $objectManager;

    protected function setUp()
    {
        $this->objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->_customerSession = $this->objectManager->get('Magento\Customer\Model\Session');
        $this->_block = $this->objectManager->get('Magento\View\LayoutInterface')
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
        /** @var CustomerAccountServiceInterface $customerAccountService */
        $customerAccountService = Bootstrap::getObjectManager()
            ->get('Magento\Customer\Service\V1\CustomerAccountServiceInterface');
        $customer = $customerAccountService->getCustomer(1);

        $this->_customerSession->setCustomerId(1);
        $object = $this->_block->getCustomer();
        $this->assertEquals($customer, $object);
    }

    public function testGetCustomerMissingCustomer()
    {
        $moduleManager = $this->objectManager->get('Magento\Module\Manager');
        if ($moduleManager->isEnabled('Magento_PageCache')) {
            $customerDtoBuilder = $this->objectManager
                ->create('Magento\Customer\Service\V1\Dto\CustomerBuilder');
            $customerDto = $customerDtoBuilder
                ->setGroupId($this->_customerSession->getCustomerGroupId())->create();
            $this->assertEquals($customerDto, $this->_block->getCustomer());
        } else {
            $this->assertNull($this->_block->getCustomer());
        }

    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoDataFixture Magento/Customer/_files/customer_two_addresses.php
     * @magentoDataFixture Magento/Customer/_files/customer_no_address.php
     * @dataProvider getPrimaryShippingAddressHtmlDataProvider
     */
    public function testGetPrimaryShippingAddressHtml($customerId, $expected)
    {
        // todo: this test is sensitive to caching impact

        if (!empty($customerId)) {
            $this->_customerSession->setCustomerId($customerId);
        }
        $html = $this->_block->getPrimaryShippingAddressHtml();
        $this->assertEquals($expected, $html);
    }

    public function getPrimaryShippingAddressHtmlDataProvider()
    {
        return [
            '0' => [0, 'You have not set a default shipping address.'],
            '1' => [
                1,
                "John Smith<br/>\n\nGreen str, 67<br />\n\n\n\nCityM,  Alabama, 75477<br/>
United States<br/>\nT: 3468676\n\n"
            ],
            '5' => [5, 'You have not set a default shipping address.'],
        ];
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoDataFixture Magento/Customer/_files/customer_two_addresses.php
     * @magentoDataFixture Magento/Customer/_files/customer_no_address.php
     * @dataProvider getPrimaryBillingAddressHtmlDataProvider
     */
    public function testGetPrimaryBillingingAddressHtml($customerId, $expected)
    {
        if (!empty($customerId)) {
            $this->_customerSession->setCustomerId($customerId);
        }
        $html = $this->_block->getPrimaryBillingAddressHtml();
        $this->assertEquals($expected, $html);
    }

    public function getPrimaryBillingAddressHtmlDataProvider()
    {
        return [
            '0' => [0, 'You have not set a default billing address.'],
            '1' => [
                1,
                "John Smith<br/>\n\nGreen str, 67<br />\n\n\n\nCityM,  Alabama, 75477<br/>
United States<br/>\nT: 3468676\n\n"
            ],
            '5' => [5, 'You have not set a default billing address.'],
        ];
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoDataFixture Magento/Customer/_files/customer_two_addresses.php
     * @magentoDataFixture Magento/Customer/_files/customer_no_address.php
     * @dataProvider getPrimaryShippingAddressEditUrlDataProvider
     */
    public function testGetPrimaryShippingAddressEditUrl($customerId, $expected)
    {
        if (!empty($customerId)) {
            $this->_customerSession->setCustomerId($customerId);
        }
        $url = $this->_block->getPrimaryShippingAddressEditUrl();
        $this->assertEquals($expected, $url);
    }

    public function getPrimaryShippingAddressEditUrlDataProvider()
    {
        return [
            '0' => [0, ''],
            '1' => [1, 'http://localhost/index.php/customer/address/edit/id/1/'],
            '5' => [5, 'http://localhost/index.php/customer/address/edit/'],
        ];
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoDataFixture Magento/Customer/_files/customer_two_addresses.php
     * @magentoDataFixture Magento/Customer/_files/customer_no_address.php
     * @dataProvider getPrimaryBillingAddressEditUrlDataProvider
     */
    public function testGetPrimaryBillingAddressEditUrl($customerId, $expected)
    {
        if (!empty($customerId)) {
            $this->_customerSession->setCustomerId($customerId);
        }
        $url = $this->_block->getPrimaryBillingAddressEditUrl();
        $this->assertEquals($expected, $url);
    }


    public function getPrimaryBillingAddressEditUrlDataProvider()
    {
        return [
            '0' => [0, ''],
            '1' => [1, 'http://localhost/index.php/customer/address/edit/id/1/'],
            '5' => [5, 'http://localhost/index.php/customer/address/edit/'],
        ];
    }
}
