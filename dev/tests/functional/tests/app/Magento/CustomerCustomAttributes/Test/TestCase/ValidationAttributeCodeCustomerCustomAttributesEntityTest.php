<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CustomerCustomAttributes\Test\TestCase;

use Mtf\TestCase\Injectable;
use Magento\CustomerCustomAttributes\Test\Fixture\CustomerCustomAttribute;
use Mtf\Fixture\FixtureFactory;
use Magento\CustomerCustomAttributes\Test\Page\Adminhtml\CustomerAttributeNew;
use Magento\CustomerCustomAttributes\Test\Page\Adminhtml\CustomerAttributeIndex;

/**
 * Test Creation for Validation AttributeCode of CustomerCustomAttributesEntity
 *
 * Test Flow:
 *
 * Preconditions:
 * 1. Create CustomerCustomAttribute
 *
 * Steps:
 * 1. Open Backend
 * 2. Go to Stores -> Attribute -> Customers
 * 3. Click Add new attribute
 * 4. Fill data according to dataSet
 * 5. Save
 * 6. Perform all assertions
 *
 * @group Customer_Attributes_(CS)
 * @ZephyrId MAGETWO-28193
 */
class ValidationAttributeCodeCustomerCustomAttributesEntityTest extends Injectable
{
    /**
     * Backend page with the list of customer attributes
     *
     * @var CustomerAttributeIndex
     */
    protected $customerAttributeIndex;

    /**
     * Backend page with new customer attribute form
     *
     * @var CustomerAttributeNew
     */
    protected $customerAttributeNew;

    /**
     * Injection data
     *
     * @param CustomerAttributeIndex $customerAttributeIndex
     * @param CustomerAttributeNew $customerAttributeNew
     * @return void
     */
    public function __inject(
        CustomerAttributeIndex $customerAttributeIndex,
        CustomerAttributeNew $customerAttributeNew
    ) {
        $this->customerAttributeIndex = $customerAttributeIndex;
        $this->customerAttributeNew = $customerAttributeNew;
    }

    /**
     * Validation AttributeCode of CustomerCustomAttributesEntity
     *
     * @param FixtureFactory $fixtureFactory
     * @param CustomerCustomAttribute $customerAttribute
     * @param CustomerCustomAttribute $initialCustomerAttribute
     * @return array
     */
    public function test(
        FixtureFactory $fixtureFactory,
        CustomerCustomAttribute $customerAttribute,
        CustomerCustomAttribute $initialCustomerAttribute
    ) {
        $this->markTestIncomplete('MAGETWO-28194');
        //Preconditions
        $initialCustomerAttribute->persist();
        $customerAttribute = $fixtureFactory->createByCode(
            'customerCustomAttribute',
            ['data' => array_replace(
                $customerAttribute->getData(),
                ['attribute_code' => $initialCustomerAttribute->getAttributeCode()]
            )]
        );
        //Steps
        $this->customerAttributeIndex->open();
        $this->customerAttributeIndex->getGridPageActions()->addNew();
        $this->customerAttributeNew->getCustomerCustomAttributesForm()->fill($customerAttribute);
        $this->customerAttributeNew->getFormPageActions()->save();

        return ['customerAttribute' => $customerAttribute];
    }
}
