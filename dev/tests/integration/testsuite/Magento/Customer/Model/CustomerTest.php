<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Model;

class CustomerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Customer\Model\Customer
     */
    protected $customerModel;

    /**
     * @var \Magento\Customer\Model\Data\CustomerBuilder
     */
    protected $customerBuilder;

    protected function setUp()
    {
        $this->customerModel = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Customer\Model\Customer'
        );
        $this->customerBuilder = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Customer\Model\Data\CustomerBuilder'
        );
    }

    public function testGetDataModel()
    {
        /** @var \Magento\Customer\Model\Data\Customer $customerData */
        $customerData = $this->customerBuilder
            ->setId(1)
            ->setFirstname('John')
            ->setLastname('Doe')
            ->setDefaultBilling(1)
            ->create();
        $updatedCustomerData = $this->customerModel->updateData($customerData)->getDataModel();

        $this->assertEquals(1, $updatedCustomerData->getId());
        $this->assertEquals('John', $updatedCustomerData->getFirstname());
        $this->assertEquals('Doe', $updatedCustomerData->getLastname());
        $this->assertEquals(1, $updatedCustomerData->getDefaultBilling());
    }
}
