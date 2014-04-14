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
class Core_Mage_Status_AssignTest extends Mage_Selenium_TestCase
{
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->navigate('order_statuses');
    }

    /**
     * <p>Create New Order Status</p>
     *
     * @test
     */
    public function preconditionsForTests()
    {
        $statusData = $this->loadDataSet('Status', 'generic_status');
        $this->storeHelper()->createStatus($statusData);
        $this->assertMessagePresent('success', 'success_saved_status');

        return $statusData;
    }

    /**
     * <p>Test navigation.</p>
     *
     * @test
     */
    public function navigation()
    {
        $this->assertTrue(
            $this->controlIsPresent('button', 'assign_status_to_state'),
            'There is no "Assign Status to State" button on the page'
        );
        $this->clickButton('assign_status_to_state');
        $this->assertTrue($this->controlIsPresent('button', 'back'), 'There is no "Back" button on the page');
        $this->assertTrue(
            $this->controlIsPresent('button', 'save_status_assignment'),
            'There is no "Save" button on the page'
        );
        $this->assertTrue($this->controlIsPresent('button', 'reset'), 'There is no "Reset" button on the page');
    }

    /**
     * <p>Assign Order Status. Fill in all required fields except one field.</p>
     *
     * @param $emptyField
     *
     * @test
     * @dataProvider withRequiredFieldsEmptyDataProvider
     */
    public function withEmptyRequiredFields($emptyField)
    {
        //Data
        $statusData = $this->loadDataSet('Status', 'assign_status', array($emptyField => '%noValue%'));
        //Steps
        $this->storeHelper()->assignStatus($statusData);
        //Verifying
        $this->addFieldIdToMessage('dropdown', $emptyField);
        $this->assertMessagePresent('validation', 'empty_required_field');
        $this->assertTrue($this->verifyMessagesCount(), $this->getParsedMessages());
    }

    public function withRequiredFieldsEmptyDataProvider()
    {
        return array(
            array('order_status'),
            array('order_state')
        );
    }

    /**
     * <p>Assign Custom Order Status to State</p>
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-1076
     */
    public function withSuccessMessage($data)
    {
        $statusData = $this->loadDataSet('Status', 'assign_status', array('order_status' => $data['status_label']));
        //Steps
        $this->storeHelper()->assignStatus($statusData);
        //Verifying
        $this->assertMessagePresent('success', 'success_assigned_status');
    }
}
