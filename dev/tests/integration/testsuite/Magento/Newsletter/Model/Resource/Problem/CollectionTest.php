<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Newsletter\Model\Resource\Problem;

class CollectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Newsletter\Model\Resource\Problem\Collection
     */
    protected $_collection;

    protected function setUp()
    {
        $this->_collection = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Newsletter\Model\Resource\Problem\Collection');
    }

    /**
     * @magentoDataFixture Magento/Newsletter/_files/problems.php
     */
    public function testAddCustomersData()
    {
        /** @var \Magento\Customer\Service\V1\CustomerAccountServiceInterface $customerAccountService */
        $customerAccountService = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Customer\Service\V1\CustomerAccountServiceInterface');
        /** @var \Magento\Customer\Service\V1\CustomerAccountServiceInterface $customerAccountService */
        $customerData = $customerAccountService->getCustomerDetails(2)->getCustomer();
        /** @var \Magento\Newsletter\Model\Subscriber $subScriber */
        $subscriber = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Newsletter\Model\Subscriber')->loadByEmail($customerData->getEmail());
        /** @var \Magento\Newsletter\Model\problem $problem */
        $problem = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Newsletter\Model\problem')->addSubscriberData($subscriber);
        /** @var Array $result */
        $result = $this->_collection->addSubscriberInfo()->load()->getFirstItem();

        $this->assertEquals($problem->getProblemErrorCode(), $result['error_code']);
        $this->assertEquals($problem->getProblemErrorText(), $result['error_text']);
        $this->assertEquals($problem->getSubscriberId(), $result['subscriber_id']);
        $this->assertEquals($customerData->getEmail(), $result['subscriber_email']);

    }

}
