<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Model\Resource;

class CustomerRepositoryTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\Customer\Api\CustomerRepositoryInterface */
    private $service;

    /** @var \Magento\Framework\ObjectManager */
    private $objectManager;

    /** @var \Magento\Customer\Api\Data\CustomerDataBuilder */
    private $customerBuilder;

    protected function setUp()
    {
        $this->objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->service = $this->objectManager->create('Magento\Customer\Api\CustomerRepositoryInterface');
        $this->customerBuilder = $this->objectManager->create('Magento\Customer\Api\Data\CustomerDataBuilder');
    }

    /**
     * @magentoDbIsolation enabled
     */
    public function testCreateNewCustomer()
    {
        $email = 'email@example.com';
        $storeId = 1;
        $firstname = 'Tester';
        $lastname = 'McTest';
        $groupId = 1;

        $this->customerBuilder
            ->setStoreId($storeId)
            ->setEmail($email)
            ->setFirstname($firstname)
            ->setLastname($lastname)
            ->setGroupId($groupId);
        $newCustomerEntity = $this->customerBuilder->create();
        $savedCustomer = $this->service->save($newCustomerEntity);
        $this->assertNotNull($savedCustomer->getId());
        $this->assertEquals($email, $savedCustomer->getEmail());
        $this->assertEquals($storeId, $savedCustomer->getStoreId());
        $this->assertEquals($firstname, $savedCustomer->getFirstname());
        $this->assertEquals($lastname, $savedCustomer->getLastname());
        $this->assertEquals($groupId, $savedCustomer->getGroupId());
        $this->assertTrue(!$savedCustomer->getSuffix());
    }
}
