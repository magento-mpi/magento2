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
 * Test creation new website
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_Store_Website_CreateTest extends Mage_Selenium_TestCase
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
        $this->assertTrue($this->controlIsPresent('button', 'create_website'),
            'There is no "Create Website" button on the page');
        $this->clickButton('create_website');
        $this->assertTrue($this->checkCurrentPage('new_website'), $this->getParsedMessages());
        $this->assertTrue($this->controlIsPresent('button', 'back'), 'There is no "Back" button on the page');
        $this->assertTrue($this->controlIsPresent('button', 'save_website'), 'There is no "Save" button on the page');
        $this->assertTrue($this->controlIsPresent('button', 'reset'), 'There is no "Reset" button on the page');
    }

    /**
     * <p>Create Website. Fill in only required fields.</p>
     *
     * @return array
     * @test
     * @depends navigation
     * @TestlinkId TL-MAGE-3618
     */
    public function withRequiredFieldsOnly()
    {
        //Data
        $websiteData = $this->loadDataSet('Website', 'generic_website');
        //Steps
        $this->storeHelper()->createStore($websiteData, 'website');
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_website');

        return $websiteData;
    }

    /**
     * <p>Create Website.  Fill in field 'Code' by using code that already exist.</p>
     *
     * @param array $websiteData
     *
     * @test
     * @depends withRequiredFieldsOnly
     * @TestlinkId TL-MAGE-3614
     */
    public function withCodeThatAlreadyExists(array $websiteData)
    {
        $this->markTestIncomplete('BUG: There is no "website_code_exist" message on the page');
        //Steps
        $this->storeHelper()->createStore($websiteData, 'website');
        //Verifying
        $this->assertMessagePresent('error', 'website_code_exist');
    }

    /**
     * <p>Create Website. Fill in all required fields except one field.</p>
     *
     * @param $emptyField
     *
     * @test
     * @dataProvider withRequiredFieldsEmptyDataProvider
     * @depends withRequiredFieldsOnly
     * @TestlinkId TL-MAGE-3617
     */
    public function withRequiredFieldsEmpty($emptyField)
    {
        //Data
        $websiteData = $this->loadDataSet('Website', 'generic_website', array($emptyField => '%noValue%'));
        //Steps
        $this->storeHelper()->createStore($websiteData, 'website');
        //Verifying
        $this->addFieldIdToMessage('field', $emptyField);
        $this->assertMessagePresent('validation', 'empty_required_field');
        $this->assertTrue($this->verifyMessagesCount(), $this->getParsedMessages());
    }

    public function withRequiredFieldsEmptyDataProvider()
    {
        return array(
            array('website_name'),
            array('website_code'),
        );
    }

    /**
     * <p>Create Website. Fill in only required fields. Use max long values for fields 'Name' and 'Code'</p>
     *
     * @test
     * @depends withRequiredFieldsOnly
     * @TestlinkId TL-MAGE-3616
     */
    public function withLongValues()
    {
        //Data
        $longValues = array('website_name' => $this->generate('string', 255, ':alnum:'),
            'website_code' => $this->generate('string', 32, ':lower:'));
        $websiteData = $this->loadDataSet('Website', 'generic_website', $longValues);
        //Steps
        $this->storeHelper()->createStore($websiteData, 'website');
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_website');
    }

    /**
     * <p>Create Website. Fill in field 'Name' by using special characters.</p>
     *
     * @test
     * @depends withRequiredFieldsOnly
     * @TestlinkId TL-MAGE-3619
     */
    public function withSpecialCharactersInName()
    {
        //Data
        $websiteData = $this->loadDataSet('Website', 'generic_website',
            array('website_name' => $this->generate('string', 32, ':punct:')));
        //Steps
        $this->storeHelper()->createStore($websiteData, 'website');
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_website');
    }

    /**
     * <p>Create Website.  Fill in field 'Code' by using wrong values.</p>
     *
     * @param $invalidCode
     *
     * @test
     * @depends withRequiredFieldsOnly
     * @dataProvider withInvalidCodeDataProvider
     * @TestlinkId TL-MAGE-3615
     */
    public function withInvalidCode($invalidCode)
    {
        $this->markTestIncomplete('BUG: There is no "wrong_website_code" message on the page');
        //Data
        $websiteData = $this->loadDataSet('Website', 'generic_website', array('website_code' => $invalidCode));
        //Steps
        $this->storeHelper()->createStore($websiteData, 'website');
        //Verifying
        $this->assertMessagePresent('error', 'wrong_website_code');
    }

    public function withInvalidCodeDataProvider()
    {
        return array(
            array('invalid code'),
            array('Invalid_code2'),
            array('2invalid_code2'),
            array($this->generate('string', 32, ':punct:'))
        );
    }

    /**
     * <p>Create Website with several Stores assigned to one Root Category</p>
     *
     * @test
     * @depends withRequiredFieldsOnly
     * @TestlinkId TL-MAGE-5347
     */
    public function withSeveralStoresAssignedToOneRootCategory()
    {
        //1.1.Create website
        //Data
        $websiteData = $this->loadDataSet('Website', 'generic_website');
        //Steps
        $this->storeHelper()->createStore($websiteData, 'website');
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_website');
        $this->assertTrue($this->checkCurrentPage('manage_stores'), $this->getParsedMessages());
        //1.2.Create two stores
        for ($i = 1; $i <= 2; $i++) {
            //Data
            $storeData = $this->loadDataSet('Store', 'generic_store', array('website' => $websiteData['website_name']));
            //Steps
            $this->storeHelper()->createStore($storeData, 'store');
            //Verifying
            $this->assertMessagePresent('success', 'success_saved_store');
            $this->assertTrue($this->checkCurrentPage('manage_stores'), $this->getParsedMessages());
        }
    }

    /**
     * <p>Create Website with several Store Views in one Store</p>
     *
     * @test
     * @depends withRequiredFieldsOnly
     * @TestlinkId TL-MAGE-5349
     */
    public function withSeveralStoresViewsInOneStore()
    {
        //1.1.Create website
        //Data
        $websiteData = $this->loadDataSet('Website', 'generic_website');
        //Steps
        $this->storeHelper()->createStore($websiteData, 'website');
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_website');
        //1.2.Create store
        //Data
        $storeData = $this->loadDataSet('Store', 'generic_store', array('website' => $websiteData['website_name']));
        //Steps
        $this->storeHelper()->createStore($storeData, 'store');
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_store');
        $this->assertTrue($this->checkCurrentPage('manage_stores'), $this->getParsedMessages());
        //1.3.Create two store view
        for ($i = 1; $i <= 2; $i++) {
            //Data
            $storeViewData =
                $this->loadDataSet('StoreView', 'generic_store_view', array('store_name' => $storeData['store_name']));
            //Steps
            $this->storeHelper()->createStore($storeViewData, 'store_view');
            //Verifying
            $this->assertMessagePresent('success', 'success_saved_store_view');
            $this->assertTrue($this->checkCurrentPage('manage_stores'), $this->getParsedMessages());
        }
    }
}