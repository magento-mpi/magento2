<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Contact\Helper;

use Magento\TestFramework\Helper\Bootstrap;

/**
 * Test for Magento\Contact\Helper\Data
 *
 * @magentoDataFixture Magento/Customer/_files/customer.php
 */
class DataTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Data
     */
    protected $contactsHelper;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * Setup customer data
     */
    protected function setUp()
    {
        $customerIdFromFixture = 1;
        $this->contactsHelper = Bootstrap::getObjectManager()->create('Magento\Contact\Helper\Data');
        $this->customerSession = Bootstrap::getObjectManager()->create('Magento\Customer\Model\Session');
        /**
         * @var $customerRepository \Magento\Customer\Api\CustomerRepositoryInterface
         */
        $customerRepository = Bootstrap::getObjectManager()->create('Magento\Customer\Api\CustomerRepositoryInterface');
        $customerData = $customerRepository->getById($customerIdFromFixture);
        $this->customerSession->setCustomerDataObject($customerData);
    }

    /**
     * Verify if username is set in session
     */
    public function testGetUserName()
    {
        $this->assertEquals('Firstname Lastname', $this->contactsHelper->getUserName());
    }

    /**
     * Verify if user email is set in session
     */
    public function testGetEmail()
    {
        $this->assertEquals('customer@example.com', $this->contactsHelper->getUserEmail());
    }

}
