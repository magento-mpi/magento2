<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_GiftWrapping
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Creation of gift wrapping
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Enterprise_Mage_GiftWrapping_CreateTest extends Mage_Selenium_TestCase
{
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->navigate('manage_gift_wrapping');
    }

    /**
     * <p>Test Case TL-MAGE-836: Adding and configuring new Gift Wrapping</p>
     *
     * @return string
     * @test
     */
    public function createWrapping()
    {
        $this->markTestIncomplete('BUG: Wrapping assigned to all websites instead of one');
        //Data
        $giftWrapping = $this->loadDataSet('GiftWrapping', 'gift_wrapping_without_image');
        $search = $this->loadDataSet('GiftWrapping', 'search_gift_wrapping',
            array('filter_gift_wrapping_design' => $giftWrapping['gift_wrapping_design']));
        $edit = $this->loadDataSet('GiftWrapping', 'edit_gift_wrapping_without_image');
        $searchEdit = $this->loadDataSet('GiftWrapping', 'search_gift_wrapping',
            array('filter_gift_wrapping_design' => $edit['gift_wrapping_design']));
        //Steps and Verification
        $this->navigate('manage_gift_wrapping');
        $this->giftWrappingHelper()->createGiftWrapping($giftWrapping);
        $this->assertMessagePresent('success', 'success_saved_gift_wrapping');
        $this->giftWrappingHelper()->openGiftWrapping($search);
        $this->giftWrappingHelper()->verifyGiftWrapping($giftWrapping);
        $this->giftWrappingHelper()->fillGiftWrappingForm($edit);
        $this->assertMessagePresent('success', 'success_saved_gift_wrapping');
        $this->giftWrappingHelper()->openGiftWrapping($searchEdit);
        $this->giftWrappingHelper()->verifyGiftWrapping($edit);

        return $edit['gift_wrapping_design'];
    }

    /**
     * <p>Test Case Test Case TL-MAGE-840: Editing/reconfiguring existing Gift Wrapping</p>
     *
     * @param string $wrappingDesign
     *
     * @test
     * @depends createWrapping
     */
    public function editWrapping($wrappingDesign)
    {
        //Data
        $giftWrapping = $this->loadDataSet('GiftWrapping', 'gift_wrapping_without_image');
        $editGiftWrapping = $this->loadDataSet('GiftWrapping', 'edit_gift_wrapping_without_image');
        $search = $this->loadDataSet('GiftWrapping', 'search_gift_wrapping',
            array('filter_gift_wrapping_design' => $giftWrapping['gift_wrapping_design']));
        $searchBefore = $this->loadDataSet('GiftWrapping', 'search_gift_wrapping',
            array('filter_gift_wrapping_design' => $wrappingDesign));
        $searchAfter = $this->loadDataSet('GiftWrapping', 'search_gift_wrapping',
            array('filter_gift_wrapping_design' => $editGiftWrapping['gift_wrapping_design']));
        //Steps
        $this->giftWrappingHelper()->openGiftWrapping($searchBefore);
        $this->giftWrappingHelper()->fillGiftWrappingForm($giftWrapping);
        $this->assertMessagePresent('success', 'success_saved_gift_wrapping');
        $this->giftWrappingHelper()->openGiftWrapping($search);
        $this->giftWrappingHelper()->verifyGiftWrapping($giftWrapping);
        $this->giftWrappingHelper()->fillGiftWrappingForm($editGiftWrapping);
        $this->assertMessagePresent('success', 'success_saved_gift_wrapping');
        $this->giftWrappingHelper()->openGiftWrapping($searchAfter);
        $this->giftWrappingHelper()->verifyGiftWrapping($editGiftWrapping);
    }

    /**
     * <p>Test Case TL-MAGE-873: Mass actions with Gift Wrappings (update statuses)</p>
     *
     * @test
     */
    public function massactionEditWrapping()
    {
        //Data
        $giftWrapping = $this->loadDataSet('GiftWrapping', 'gift_wrapping_without_image');
        $search = $this->loadDataSet('GiftWrapping', 'search_gift_wrapping',
            array('filter_gift_wrapping_design' => $giftWrapping['gift_wrapping_design']));
        $this->addParameter('itemCount', '1');
        //Steps
        $this->giftWrappingHelper()->createGiftWrapping($giftWrapping);
        $this->assertMessagePresent('success', 'success_saved_gift_wrapping');
        $this->searchAndChoose($search, 'gift_wrapping_grid');
        $this->fillDropdown('massaction_action', 'Change status');
        $this->fillDropdown('massaction_status', 'Disabled');
        $this->saveForm('submit');
        //Verification
        $this->assertMessagePresent('success', 'success_massaction_update');
        //Steps
        $this->navigate('manage_gift_wrapping');
        $this->searchAndChoose($search, 'gift_wrapping_grid');
        $this->fillDropdown('massaction_action', 'Change status');
        $this->fillDropdown('massaction_status', 'Enabled');
        $this->saveForm('submit');
        //Verification
        $this->assertMessagePresent('success', 'success_massaction_update');
    }

    /**
     * <p>Test Case:</p>
     *
     * @param string $fieldName
     *
     * @test
     * @dataProvider createWrappingWithEmptyFieldsDataProvider
     */
    public function createWrappingWithEmptyFields($fieldName)
    {
        //Data
        $giftWrappingData = $this->loadDataSet('GiftWrapping', 'gift_wrapping_without_image', array($fieldName => ''));
        //Steps
        $this->navigate('manage_gift_wrapping');
        $this->giftWrappingHelper()->createGiftWrapping($giftWrappingData);
        //Verification
        $this->addFieldIdToMessage('field', $fieldName);
        $this->assertMessagePresent('validation', 'empty_required_field');
        $this->assertTrue($this->verifyMessagesCount(), $this->getParsedMessages());
    }

    public function createWrappingWithEmptyFieldsDataProvider()
    {
        return array(
            array('gift_wrapping_design'),
            array('gift_wrapping_price')
        );
    }

    /**
     * <p>Test Case:</p>
     *
     * @param string $fieldData
     *
     * @test
     * @dataProvider incorrectPriceDataProvider
     */
    public function createWrappingWithIncorrectPrice($fieldData)
    {
        //Data
        $giftWrapping = $this->loadDataSet('GiftWrapping', 'gift_wrapping_without_image',
            array('gift_wrapping_price' => $fieldData));
        //Steps
        $this->giftWrappingHelper()->createGiftWrapping($giftWrapping);
        //Verification
        $this->addFieldIdToMessage('field', 'gift_wrapping_price');
        $this->assertMessagePresent('validation', 'enter_zero_or_greater');
        $this->assertTrue($this->verifyMessagesCount(), $this->getParsedMessages());
    }

    public function incorrectPriceDataProvider()
    {
        return array(
            array('-10'),
            array('abc')
        );
    }
}
