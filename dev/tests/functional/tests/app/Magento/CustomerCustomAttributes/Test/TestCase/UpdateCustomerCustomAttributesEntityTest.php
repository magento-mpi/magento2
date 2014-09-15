<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CustomerCustomAttributes\Test\TestCase;

use Magento\CustomerCustomAttributes\Test\Fixture\CustomerCustomAttribute;

/**
 * Test Creation UpdateCustomerCustomAttributesEntity
 *
 * Test Flow:
 *
 * Preconditions:
 * 1. Create customer attribute
 *
 * Steps:
 * 1. Open Backend
 * 2. Go to Stores> Customer
 * 3. Open created customer attribute
 * 4. Fill data according to dataset
 * 5. Save attribute
 * 6. Perform all assertions
 *
 * @group Customer_Attributes_(CS)
 * @ZephyrId MAGETWO-26366
 */
class UpdateCustomerCustomAttributesEntityTest extends AbstractCustomerCustomAttributesEntityTest
{
    /**
     * Update custom Customer Attribute
     *
     * @param CustomerCustomAttribute $customerAttribute
     * @param CustomerCustomAttribute $initialCustomerAttribute
     * @return array
     */
    public function test(
        CustomerCustomAttribute $customerAttribute,
        CustomerCustomAttribute $initialCustomerAttribute
    ) {
        $this->markTestIncomplete('MAGETWO-18664');
        // Preconditions
        $initialCustomerAttribute->persist();

        // Steps
        $filter = ['attribute_code' => $initialCustomerAttribute->getAttributeCode()];
        $this->customerAttributeIndex->open();
        $this->customerAttributeIndex->getCustomerCustomAttributesGrid()->searchAndOpen($filter);
        $this->customerAttributeNew->getCustomerCustomAttributesForm()->fill($customerAttribute);
        $this->customerAttributeNew->getFormPageActions()->save();

        // Prepare data for tear down
        $this->customerCustomAttribute = $customerAttribute;
    }
}
