<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Store
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test creation new store view
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_Store_StoreView_CreateTest extends Mage_Selenium_TestCase
{
    /**
     * <p>Preconditions:</p>
     * <p>Navigate to System -> Manage Stores</p>
     */
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->navigate('manage_stores');
    }

    public function tearDownAfterTestClass()
    {
        $this->loginAdminUser();
        $this->navigate('manage_stores');
        $this->storeHelper()->deleteAllStoresExceptSpecified();
    }

    /**
     * <p>Test navigation.</p>
     *
     * @test
     */
    public function navigation()
    {
        $this->assertTrue($this->controlIsPresent('button', 'create_store_view'),
            'There is no "Create Store View" button on the page');
        $this->clickButton('create_store_view');
        $this->assertTrue($this->checkCurrentPage('new_store_view'), $this->getParsedMessages());
        $this->assertTrue($this->controlIsPresent('button', 'back'), 'There is no "Back" button on the page');
        $this->assertTrue($this->controlIsPresent('button', 'save_store_view'),
            'There is no "Save" button on the page');
        $this->assertTrue($this->controlIsPresent('button', 'reset'), 'There is no "Reset" button on the page');
    }

    /**
     * <p>Create Store View. Fill in only required fields.</p>
     *
     * @return array
     * @test
     * @depends navigation
     * @TestlinkId TL-MAGE-3628
     */
    public function withRequiredFieldsOnly()
    {
        //Data
        $storeViewData = $this->loadDataSet('StoreView', 'generic_store_view');
        //Steps
        $this->storeHelper()->createStore($storeViewData, 'store_view');
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_store_view');

        return $storeViewData;
    }

    /**
     * <p>Create Store View.  Fill in field 'Code' by using code that already exist.</p>
     *
     * @param array $storeViewData
     *
     * @test
     * @depends withRequiredFieldsOnly
     * @TestlinkId TL-MAGE-3624
     */
    public function withCodeThatAlreadyExists(array $storeViewData)
    {
        //Steps
        $this->markTestIncomplete('BUG: There is no "store_view_code_exist" message on the page');
        $this->storeHelper()->createStore($storeViewData, 'store_view');
        //Verifying
        $this->assertMessagePresent('error', 'store_view_code_exist');
    }

    /**
     * <p>Create Store View. Fill in  required fields except one field.</p>
     *
     * @param $emptyField
     *
     * @test
     * @dataProvider withRequiredFieldsEmptyDataProvider
     * @depends withRequiredFieldsOnly
     * @TestlinkId TL-MAGE-3627
     */
    public function withRequiredFieldsEmpty($emptyField)
    {
        //Data
        $storeViewData = $this->loadDataSet('StoreView', 'generic_store_view', array($emptyField => '%noValue%'));
        //Steps
        $this->storeHelper()->createStore($storeViewData, 'store_view');
        //Verifying
        $this->addFieldIdToMessage('field', $emptyField);
        $this->assertMessagePresent('validation', 'empty_required_field');
        $this->assertTrue($this->verifyMessagesCount(), $this->getParsedMessages());
    }

    public function withRequiredFieldsEmptyDataProvider()
    {
        return array(
            array('store_view_name'),
            array('store_view_code'),
        );
    }

    /**
     * <p>Create Store View. Fill in only required fields. Use max long values for fields 'Name' and 'Code'</p>
     *
     * @test
     * @depends withRequiredFieldsOnly
     * @TestlinkId TL-MAGE-3626
     */
    public function withLongValues()
    {
        //Data
        $longValues = array('store_view_name' => $this->generate('string', 255, ':alnum:'),
            'store_view_code' => $this->generate('string', 32, ':lower:'));
        $storeViewData = $this->loadDataSet('StoreView', 'generic_store_view', $longValues);
        //Steps
        $this->storeHelper()->createStore($storeViewData, 'store_view');
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_store_view');
    }

    /**
     * <p>Create Store View. Fill in field 'Name' by using special characters.</p>
     *
     * @test
     * @depends withRequiredFieldsOnly
     * @TestlinkId TL-MAGE-3630
     */
    public function withSpecialCharactersInName()
    {
        //Data
        $storeViewData = $this->loadDataSet('StoreView', 'generic_store_view',
            array('store_view_name' => $this->generate('string', 32, ':punct:')));
        //Steps
        $this->storeHelper()->createStore($storeViewData, 'store_view');
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_store_view');
    }

    /**
     * <p>Create Store View.  Fill in field 'Code' by using special characters.</p>
     *
     * @test
     * @depends withRequiredFieldsOnly
     * @TestlinkId TL-MAGE-3629
     */
    public function withSpecialCharactersInCode()
    {
        //Data
        $storeViewData = $this->loadDataSet('StoreView', 'generic_store_view',
            array('store_view_code' => $this->generate('string', 32, ':punct:')));
        //Steps
        $this->storeHelper()->createStore($storeViewData, 'store_view');
        //Verifying
        $this->assertMessagePresent('error', 'wrong_store_view_code');
    }

    /**
     * <p>Create Store View.  Fill in field 'Code' by using wrong values.</p>
     *
     * @param $invalidCode
     *
     * @test
     * @dataProvider withInvalidCodeDataProvider
     * @depends withRequiredFieldsOnly
     * @TestlinkId TL-MAGE-3625
     */
    public function withInvalidCode($invalidCode)
    {
        //Data
        $storeView = $this->loadDataSet('StoreView', 'generic_store_view', array('store_view_code' => $invalidCode));
        //Steps
        $this->storeHelper()->createStore($storeView, 'store_view');
        //Verifying
        $this->assertMessagePresent('error', 'wrong_store_view_code');
    }

    public function withInvalidCodeDataProvider()
    {
        return array(
            array('invalid code'),
            array('Invalid_code2'),
            array('2invalid_code2')
        );
    }
}