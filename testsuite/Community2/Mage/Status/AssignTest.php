<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Status
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Assignment Order Status
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Community2_Mage_Status_AssignTest extends Mage_Selenium_TestCase
{

    /**
     * <p>Preconditions:</p>
     * <p>Navigate to System -> Order Statuses</p>
     */
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->navigate('order_statuses');
    }

    /**
     * <p>Create New Order Status</p>
     *
     * @test
     *
     */
    public function newOrderStatusCreate()
    {
        $statusData = $this->loadDataSet('Status', 'generic_status');
        $this->storeHelper()->createStatus($statusData);
        $this->assertMessagePresent('success', 'success_saved_status');

        return $statusData;
    }

    /**
     * <p>Test navigation.</p>
     * <p>Steps:</p>
     * <p>1. Verify that 'Assign Status to State' button is present and click it.</p>
     * <p>2. Verify that the Assign Order Status to State page is opened.</p>
     * <p>3. Verify that 'Back' button is present.</p>
     * <p>4. Verify that 'Save Status Assignment' button is present.</p>
     * <p>5. Verify that 'Reset' button is present.</p>
     *
     * @test
     */
    public function navigation()
    {
        $this->assertTrue($this->controlIsPresent('button', 'assign_status_to_state'),
            'There is no "Assign Status to State" button on the page');
        $this->clickButton('assign_status_to_state');
        $this->assertTrue($this->controlIsPresent('button', 'back'), 'There is no "Back" button on the page');
        $this->assertTrue($this->controlIsPresent('button', 'save_status_assignment'),
            'There is no "Save" button on the page');
        $this->assertTrue($this->controlIsPresent('button', 'reset'), 'There is no "Reset" button on the page');
    }

    /**
     * <p>Assign Custom Order Status to State</p>
     * <p>Steps:</p>
     * <p>1. Click 'Assign Status to State' button.</p>
     * <p>2. Select Order Status from Order Status drop-down</p>
     * <p>3. Select Order State from Order State drop-down</p>
     * <p>4. Press 'Save Status Assignment' button
     * <p>Expected result:</p>
     * <p>Status is assigned</p>
     * <p>Success Message is displayed</p>
     *
     * @test
     * @depends newOrderStatusCreate
     * @TestlinkId TL-MAGE-1076
     */
    public function withSuccessMessage()
    {
        $statusData = $this->loadDataSet('Status', 'assign_status');
        //Steps
        $this->storeHelper()->assignStatus($statusData);
        //Verifying
        $this->assertMessagePresent('success', 'success_assigned_status');
    }

    /**
     * <p>Assign Order Status. Fill in all required fields except one field.</p>
     * <p>Steps:</p>
     * <p>1. Click 'Assign Status to State' button.</p>
     * <p>2. Fill in required fields except one field.</p>
     * <p>3. Click 'Save Status Assignment' button.</p>
     * <p>Expected result:</p>
     * <p>Status is not assigned.</p>
     * <p>Error Message is displayed.</p>
     *
     * @param $emptyField
     * @param $fieldType
     *
     * @test
     * @dataProvider withRequiredFieldsEmptyDataProvider
     */
    public function withEmptyRequiredFields($emptyField, $fieldType)
    {
        //Data
        $statusData = $this->loadDataSet('Status', 'assign_status', array($emptyField => '%noValue%'));
        //Steps
        $this->storeHelper()->assignStatus($statusData);
        //Verifying
        $xpath = $this->_getControlXpath($fieldType, $emptyField);
        $this->addParameter('fieldXpath', $xpath);
        $this->assertMessagePresent('error', 'empty_required_field');
        $this->assertTrue($this->verifyMessagesCount(), $this->getParsedMessages());
    }

    /**
     * <p>Data for withEmptyRequiredFields</p>
     * @return array
     */
    public function withRequiredFieldsEmptyDataProvider()
    {
        return array(
          array('order_status', 'dropdown'),
          array('order_state', 'dropdown'));
    }
}
