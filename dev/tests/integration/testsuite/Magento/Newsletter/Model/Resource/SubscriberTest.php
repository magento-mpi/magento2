<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Newsletter\Model\Resource;

use Magento\TestFramework\Helper\Bootstrap;

class SubscriberTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Newsletter\Model\Resource\Subscriber
     */
    protected $_resourceModel;

    protected function setUp()
    {
        $this->_resourceModel = Bootstrap::getObjectManager()
            ->create('Magento\Newsletter\Model\Resource\Subscriber');
    }

    /**
     * @magentoDataFixture Magento/Newsletter/_files/subscribers.php
     */
    public function testLoadByCustomerDataWithCustomerId()
    {
        /** @var \Magento\Customer\Api\CustomerRepositoryInterface $customerAccountService */
        $customerAccountService = Bootstrap::getObjectManager()
            ->create('Magento\Customer\Api\CustomerRepositoryInterface');
        $customerData = $customerAccountService->getById(1);
        $result = $this->_resourceModel->loadByCustomerData($customerData);

        $this->assertEquals(1, $result['customer_id']);
        $this->assertEquals('customer@example.com', $result['subscriber_email']);
    }

    /**
     * @magentoDataFixture Magento/Newsletter/_files/subscribers.php
     * @magentoDataFixture Magento/Customer/_files/two_customers.php
     */
    public function testLoadByCustomerDataWithoutCustomerId()
    {
        /** @var \Magento\Customer\Api\CustomerRepositoryInterface $customerAccountService */
        $customerAccountService = Bootstrap::getObjectManager()
            ->create('Magento\Customer\Api\CustomerRepositoryInterface');
        $customerData = $customerAccountService->getById(2);
        $result = $this->_resourceModel->loadByCustomerData($customerData);

        $this->assertEquals(0, $result['customer_id']);
        $this->assertEquals('customer_two@example.com', $result['subscriber_email']);
    }
}
