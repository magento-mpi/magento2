<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Helper;

use Magento\TestFramework\Helper\Bootstrap;

class DataTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\Customer\Helper\Data */
    protected $_dataHelper;

    /** @var \Magento\Customer\Model\Session */
    protected $_customerSession;

    protected function setUp()
    {
        $this->_dataHelper = Bootstrap::getObjectManager()->create('Magento\Customer\Helper\Data');
        $this->_customerSession = Bootstrap::getObjectManager()->get('Magento\Customer\Model\Session');
        $this->_customerSession->setCustomerId(1);
        parent::setUp();
    }

    /**
     * Clean up shared dependencies
     */
    protected function tearDown()
    {
        /** @var \Magento\Customer\Model\CustomerRegistry $customerRegistry */
        $customerRegistry = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Customer\Model\CustomerRegistry');
        //Cleanup customer from registry
        $customerRegistry->remove(1);
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testGetCustomerData()
    {
        $this->assertInstanceOf('\Magento\Customer\Service\V1\Data\Customer', $this->_dataHelper->getCustomerData());
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testCustomerHasNoAddresses()
    {
        $this->assertFalse($this->_dataHelper->customerHasAddresses());
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoDataFixture Magento/Customer/_files/customer_address.php
     */
    public function testCustomerHasAddresses()
    {
        $this->assertTrue($this->_dataHelper->customerHasAddresses());
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testConfirmationNotRequired()
    {
        $this->assertFalse($this->_dataHelper->isConfirmationRequired());
    }
}
 
