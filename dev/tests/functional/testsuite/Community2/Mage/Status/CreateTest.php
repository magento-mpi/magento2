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
 * Test creation new status
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Community2_Mage_Status_CreateTest extends Mage_Selenium_TestCase
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
     * <p>Test navigation.</p>
     * <p>Steps:</p>
     * <p>1. Verify that 'Create New Status' button is present and click her.</p>
     * <p>2. Verify that the new order status page is opened.</p>
     * <p>3. Verify that 'Back' button is present.</p>
     * <p>4. Verify that 'Save Status' button is present.</p>
     * <p>5. Verify that 'Reset' button is present.</p>
     *
     * @test
     */
    public function navigation()
    {
        $this->assertTrue($this->controlIsPresent('button', 'create_new_status'),
            'There is no "Create Status" button on the page');
        $this->clickButton('create_new_status');
        $this->assertTrue($this->controlIsPresent('button', 'back'), 'There is no "Back" button on the page');
        $this->assertTrue($this->controlIsPresent('button', 'save_status'), 'There is no "Save" button on the page');
        $this->assertTrue($this->controlIsPresent('button', 'reset'), 'There is no "Reset" button on the page');
    }

    /**
     * <p>Create Custom Order Status. Fill in only required fields.</p>
     * <p>Steps:</p>
     * <p>1. Click 'Create New Status' button.</p>
     * <p>2. Fill in required fields.</p>
     * <p>3. Click 'Save Status' button.</p>
     * <p>Expected result:</p>
     * <p>Status is created.</p>
     * <p>Success Message is displayed</p>
     *
     * @test
     * @TestlinkId TL-MAGE-1048
     */
    public function withRequiredFieldsOnly()
    {
        //Data
        $statusData = $this->loadDataSet('Status', 'generic_status');
        //Steps
        $this->storeHelper()->createStatus($statusData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_status');
    }

    /**
     * <p>Create New Order Status.  Fill in field 'Code' by using code that already exist.</p>
     * <p>Steps:</p>
     * <p>1. Click 'Create new Order Status' button.</p>
     * <p>2. Fill in 'Code' field by using code that already exist.</p>
     * <p>3. Fill other required fields by regular data.</p>
     * <p>4. Click 'Save Status' button.</p>
     * <p>Expected result:</p>
     * <p>Status is not created.</p>
     * <p>Error Message is displayed.</p>
     *
     * @param array $statusData
     *
     * @test
     * @depends withRequiredFieldsOnly
     *
     */
    public function withCodeThatAlreadyExists($statusData)
    {
        //Steps
        $this->storeHelper()->createStatus($statusData);
        //Verifying
        $this->assertMessagePresent('error', 'order_status_exist');
    }

    /**
     * <p>Create New Order Status. Fill in all required fields except one field.</p>
     * <p>Steps:</p>
     * <p>1. Click 'Create New Order Status' button.</p>
     * <p>2. Fill in required fields except one field.</p>
     * <p>3. Click 'Save Order' button.</p>
     * <p>Expected result:</p>
     * <p>Status is not created.</p>
     * <p>Error Message is displayed.</p>
     *
     * @param $emptyField
     *
     * @test
     *
     * @dataProvider withRequiredFieldsEmptyDataProvider
     *
     * @depends withRequiredFieldsOnly
     *
     */
    public function withRequiredFieldsEmpty($emptyField)
    {
        //Data
        $statusData = $this->loadDataSet('Status', 'generic_status', array($emptyField => '%noValue%'));
        //Steps
        $this->storeHelper()->createStatus($statusData);
        //Verifying
        $xpath = $this->_getControlXpath('field', $emptyField);
        $this->addParameter('fieldXpath', $xpath);
        $this->assertMessagePresent('error', 'empty_required_field');
        $this->assertTrue($this->verifyMessagesCount(), $this->getParsedMessages());
    }

    public function withRequiredFieldsEmptyDataProvider()
    {
        return array(
          array('status_label'),
          array('status_code'),);
    }

    /**
     * <p>Create New Status Order. Fill in field 'Label' by using special characters.</p>
     * <p>Steps:</p>
     * <p>1. Click 'New Status Order' button.</p>
     * <p>2. Fill in 'Label' field by special characters.</p>
     * <p>3. Fill other required fields by regular data.</p>
     * <p>4. Click 'Save Status' button.</p>
     * <p>Expected result:</p>
     * <p>New Status Order is created.</p>
     * <p>Success Message is displayed</p>
     *
     * @test
     *
     * @depends withRequiredFieldsOnly
     */
    public function withSpecialCharactersInLabel()
    {
        //Data
        $statusData = $this->loadDataSet('Status', 'generic_status',
            array('status_label' => $this->generate('string', 32, ':punct:')));
        //Steps
        $this->storeHelper()->createStatus($statusData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_status');
    }

    /**
     * <p>Create New Order Status.  Fill in field "Code" by using wrong values.</p>
     * <p>Steps:</p>
     * <p>1. Click 'New Order Status' button.</p>
     * <p>2. Fill in 'Code' field by wrong value.</p>
     * <p>3. Fill other required fields by regular data.</p>
     * <p>4. Click 'Save Status' button.</p>
     * <p>Expected result:</p>
     * <p>New Order Status is not created.</p>
     * <p>Error Message is displayed.</p>
     *
     * @param $invalidCode
     *
     * @test
     * @depends withRequiredFieldsOnly
     * @dataProvider withInvalidCodeDataProvider
     *
     */
    public function withWrongCode($invalidCode)
    {
        //Data
        $statusData = $this->loadDataSet('Status', 'generic_status', array('status_code' => $invalidCode));
        //Steps
        $this->storeHelper()->createStatus($statusData);
        //Verifying
        $this->assertMessagePresent('error', 'wrong_status_view_code');
    }

    public function withInvalidCodeDataProvider()
    {

        return array(
          array('invalid code'),
          array('Invalid_code2'),
          array('2invalid_code2'),
          array($this->generate('string', 32, ':punct:')));
    }
}
