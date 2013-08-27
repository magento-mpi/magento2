<?php
/**
 * Test customer group Api.
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
class Magento_Customer_Model_Group_ApiTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test item method.
     */
    public function testList()
    {
        /** Retrieve the list of customer groups. */
        $groupsList = Magento_Test_Helper_Api::call($this, 'customerGroupList', array());
        /** Assert customers group list is not empty. */
        $this->assertNotEmpty($groupsList, 'Customers list retrieving was unsuccessful.');
        /** Assert base fields are present in the response. */
        $groupInfo = reset($groupsList);
        $expectedFields = array('customer_group_id', 'customer_group_code');
        $missingFields = array_diff($expectedFields, array_keys($groupInfo));
        $this->assertEmpty(
            $missingFields,
            sprintf("The following fields must be present in response: %s.", implode(', ', $missingFields))
        );
    }
}
