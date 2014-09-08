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
 * Test Creation for CreateCustomerCustomAttributesEntity
 *
 * Test Flow:
 * 1. Log in to Backend
 * 2. Navigate to Stores > Customer
 * 3. Click "Add New Attribute"
 * 4. Fill data according to dataset
 * 5. Save attribute
 * 6. Perform all assertions
 *
 * @group Customer_Attributes_(CS)
 * @ZephyrId MAGETWO-25963
 */
class CreateCustomerCustomAttributesEntityTest extends AbstractCustomerCustomAttributesEntityTest
{
    /**
     * Create custom Customer Attribute
     *
     * @param CustomerCustomAttribute $customerAttribute
     * @return array
     */
    public function test(CustomerCustomAttribute $customerAttribute)
    {
        $this->markTestIncomplete('MAGETWO-18664');
        // Steps
        $this->customerAttributeIndex->open();
        $this->customerAttributeIndex->getGridPageActions()->addNew();
        $this->customerAttributeNew->getCustomerCustomAttributesForm()->fill($customerAttribute);
        $this->customerAttributeNew->getFormPageActions()->save();

        // Prepare data for tear down
        $this->customerCustomAttribute = $customerAttribute;

        return ['customerAttribute' => $customerAttribute];
    }
}
