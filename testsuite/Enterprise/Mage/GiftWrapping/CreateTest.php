<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    tests
 * @package     selenium
 * @subpackage  tests
 * @author      Magento Core Team <core@magentocommerce.com>
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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

    /**
     * <p>Preconditions:</p>
     * <p>Log in to Backend.</p>
     * <p>Navigate to System -> Manage Gift Wrapping</p>
     */
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->navigate('manage_gift_wrapping');
    }

    /**
     * <p>Test Case:</p>
     * <p>Steps:</p>
     * <p>1. Navigate to "Manage Gift Wrapping" page;</p>
     * <p>2. Click button "Add Gift Wrapping";</p>
     * <p>3. Fill all required fields except one (from data provider);</p>
     * <p>4. Save gift wrapping</p>
     * <p>Expected Results:</p>
     * <p>1. Gift wrapping is not created;</p>
     * <p>2. Message "This is a required field." for required field appears.</p>
     *
     * @dataProvider createWrappingWithEmptyFieldsDataProvider
     * @param string $fieldName
     * @param string $fieldType
     *
     * @test
     */
    public function createWrappingWithEmptyFields($fieldName, $fieldType)
    {
        //Data
        $giftWrappingData = $this->loadDataSet('GiftWrapping', 'gift_wrapping_without_image', array($fieldName => ''));
        //Steps
        $this->navigate('manage_gift_wrapping');
        $this->giftWrappingHelper()->createGiftWrapping($giftWrappingData);
        //Verification
        $this->addFieldIdToMessage($fieldType, $fieldName);
        $this->assertMessagePresent('validation', 'empty_required_field');
        $this->assertTrue($this->verifyMessagesCount(), $this->getParsedMessages());
    }

    public function createWrappingWithEmptyFieldsDataProvider()
    {
        return array(
            array('gift_wrapping_design', 'field'),
            array('gift_wrapping_price', 'field')
        );
    }

    /**
     * <p>Test Case:</p>
     * <p>Steps:</p>
     * <p>1. Navigate to "Manage Gift Wrapping" page;</p>
     * <p>2. Click button "Add Gift Wrapping";</p>
     * <p>3. Fill all required fields with correct data except price (enter "-10");</p>
     * <p>4. Save gift wrapping</p>
     * <p>Expected Results:</p>
     * <p>1. Gift wrapping is not created;</p>
     * <p>2. Message "Please enter a valid number in this field." for price field appears.</p>
     *
     * @dataProvider incorrectPriceDataProvider
     * @param string $fieldName
     * @param string $fieldData
     * @param string $messageName
     *
     * @test
     */
    public function createWrappingWithIncorrectPrice($fieldName, $fieldData, $messageName)
    {
        //Data
        $giftWrappingData = $this->loadDataSet('GiftWrapping', 'gift_wrapping_without_image',
                                               array($fieldName => $fieldData));
        //Steps
        $this->navigate('manage_gift_wrapping');
        $this->giftWrappingHelper()->createGiftWrapping($giftWrappingData);
        //Verification
        $this->addFieldIdToMessage('field', 'gift_wrapping_price');
        $this->assertMessagePresent('validation', $messageName);
        $this->assertTrue($this->verifyMessagesCount(), $this->getParsedMessages());
    }

    public function incorrectPriceDataProvider()
    {
        return array(
            array('gift_wrapping_price', '-10', 'enter_not_negative_number'),
            array('gift_wrapping_price', ' ', 'empty_required_field')
        );
    }

    /**
     * <p>Test Case TL-MAGE-836: Adding and configuring new Gift Wrapping</p>
     * <p>Steps:</p>
     * <p>1. Navigate to "Manage Gift Wrapping" page;</p>
     * <p>2. Click button "Add Gift Wrapping";</p>
     * <p>3. Fill all required fields with correct data;</p>
     * <p>4. Press button "Save and Continue";</p>
     * <p>5. Save gift wrapping</p>
     * <p>Expected Results:</p>
     * <p>1. Gift wrapping is created;</p>
     *
     * @return string
     *
     * @test
     */
    public function createWrapping()
    {
        //Data
        $giftWrappingData = $this->loadDataSet('GiftWrapping', 'gift_wrapping_without_image');
        $editGiftWrappingData = $this->loadDataSet('GiftWrapping', 'edit_gift_wrapping_without_image');
        $searchGiftWrapping = $this->loadDataSet('GiftWrapping', 'search_gift_wrapping',
            array('filter_gift_wrapping_design' => $editGiftWrappingData['gift_wrapping_design']));
        //Steps
        $this->navigate('manage_gift_wrapping');
        $this->giftWrappingHelper()->createGiftWrapping($giftWrappingData, false);
        $this->addParameter('storeId', '0');
        $this->addParameter('elementTitle', $giftWrappingData['gift_wrapping_design']);
        $this->saveForm('save_and_continue_edit');
        //Verification
        $this->assertMessagePresent('success', 'success_saved_gift_wrapping');
        $this->giftWrappingHelper()->verifyGiftWrapping($giftWrappingData);
        //Steps
        $this->giftWrappingHelper()->fillGiftWrappingForm($editGiftWrappingData);
        //Verification
        $this->assertMessagePresent('success', 'success_saved_gift_wrapping');
        $this->giftWrappingHelper()->openGiftWrapping($searchGiftWrapping);
        $this->giftWrappingHelper()->verifyGiftWrapping($editGiftWrappingData);

        return $editGiftWrappingData['gift_wrapping_design'];
    }

    /**
     * <p>Test Case Test Case TL-MAGE-840: Editing/reconfiguring existing Gift Wrapping</p>
     * <p>Steps:</p>
     * <p>1. Navigate to "Manage Gift Wrapping" page;</p>
     * <p>2. Open previously created gift wrapping;</p>
     * <p>3. Change gift wrapping configuration to new data;</p>
     * <p>4. Save gift wrapping</p>
     * <p>Expected Results:</p>
     * <p>1. Gift wrapping is saved;</p>
     *
     * @depends createWrapping
     * @param string $wrappingDesign
     *
     * @test
     */
    public function editWrapping($wrappingDesign)
    {
        //Data
        $giftWrappingData = $this->loadDataSet('GiftWrapping', 'gift_wrapping_without_image');
        $editGiftWrappingData = $this->loadDataSet('GiftWrapping', 'edit_gift_wrapping_without_image');
        $searchGiftWrappingBefore = $this->loadDataSet('GiftWrapping', 'search_gift_wrapping',
                    array('filter_gift_wrapping_design' => $wrappingDesign));
        $searchGiftWrappingAfter = $this->loadDataSet('GiftWrapping', 'search_gift_wrapping',
                    array('filter_gift_wrapping_design' => $editGiftWrappingData['gift_wrapping_design']));
        //Steps
        $this->navigate('manage_gift_wrapping');
        $this->giftWrappingHelper()->openGiftWrapping($searchGiftWrappingBefore);
        $this->giftWrappingHelper()->fillGiftWrappingForm($giftWrappingData, false);
        $this->addParameter('storeId', '0');
        $this->addParameter('elementTitle', $giftWrappingData['gift_wrapping_design']);
        $this->saveForm('save_and_continue_edit');
        //Verification
        $this->assertMessagePresent('success', 'success_saved_gift_wrapping');
        $this->giftWrappingHelper()->verifyGiftWrapping($giftWrappingData);
        //Steps
        $this->giftWrappingHelper()->fillGiftWrappingForm($editGiftWrappingData);
        //Verification
        $this->assertMessagePresent('success', 'success_saved_gift_wrapping');
        $this->giftWrappingHelper()->openGiftWrapping($searchGiftWrappingAfter);
        $this->giftWrappingHelper()->verifyGiftWrapping($editGiftWrappingData);
    }

    /**
     * <p>Test Case TL-MAGE-873: Mass actions with Gift Wrappings (update statuses)</p>
     * <p>Steps:</p>
     * <p>1. Navigate to "Manage Gift Wrapping" page;</p>
     * <p>2. Select previously created gift wrapping by checking checkbox;</p>
     * <p>3. Choose massaction action "Change status";</p>
     * <p>4. Choose massaction status "Disable";</p>
     * <p>5. Submit action.</p>
     * <p>6. Choose massaction action "Change status";</p>
     * <p>7. Choose massaction status "Enable";</p>
     * <p>8. Submit action.</p>
     * <p>Expected Results:</p>
     * <p>1. Gift wrapping is updated;</p>
     *
     * @test
     */
    public function massactionEditWrapping()
    {
        //Preconditions
        $giftWrappingData = $this->loadDataSet('GiftWrapping', 'gift_wrapping_without_image');
        $this->navigate('manage_gift_wrapping');
        $this->giftWrappingHelper()->createGiftWrapping($giftWrappingData);
        $this->assertMessagePresent('success', 'success_saved_gift_wrapping');
        //Steps
        $this->navigate('manage_gift_wrapping');
        $this->searchAndChoose(array('filter_gift_wrapping_design' => $giftWrappingData['gift_wrapping_design']));
        $this->fillForm(array('massaction_action' => 'Change status', 'massaction_status' => 'Disabled'));
        $this->addParameter('itemCount', '1');
        $this->saveForm('submit');
        //Verification
        $this->assertMessagePresent('success', 'success_massaction_update');
        //Steps
        $this->navigate('manage_gift_wrapping');
        $this->searchAndChoose(array('filter_gift_wrapping_design' => $giftWrappingData['gift_wrapping_design']));
        $this->fillForm(array('massaction_action' => 'Change status', 'massaction_status' => 'Enabled'));
        $this->addParameter('itemCount', '1');
        $this->saveForm('submit');
        //Verification
        $this->assertMessagePresent('success', 'success_massaction_update');

    }
}
