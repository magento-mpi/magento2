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
# @category    Magento
# @package     Mage_GiftRegistry
# @subpackage  functional_tests
 * @author      Magento Core Team <core@magentocommerce.com>
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Gift Registry creation into backend
 *
 * @package     Mage_CmsWidget
 * @subpackage  functional_tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Enterprise2_Mage_GiftRegistry_CreateTest extends Mage_Selenium_TestCase
{
    /**
     * <p>Preconditions:</p>
     * <p>Log in to Backend.</p>
     * <p>Navigate to Customers -> Gift Registry./p>
     */
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->navigate('manage_gift_registry');
    }

    /**
     * <p>Navigation test.</p>
     * <p>Steps:</p>
     * <p>1. Verify that 'Add New Gift Registry Type' button is present and click her.</p>
     * <p>2. Verify that the create gift registry is opened.</p>
     * <p>3. Verify that 'Back' button is present.</p>
     * <p>4. Verify that 'Save' button is present.</p>
     * <p>5. Verify that 'Save and Continue Edit' button is present.</p>
     * <p>6. Verify that 'Reset' button is present.</p>
     *
     * @test
     */
    public function navigationTest()
    {
        $this->assertTrue($this->buttonIsPresent('add_new_gift_registry_type'),
            'There is no "Add New Gift Registry Type" button on the page');
        $this->clickButton('add_new_gift_registry_type');
        $this->assertTrue($this->checkCurrentPage('new_gift_registry'), $this->getParsedMessages());
        $this->assertTrue($this->buttonIsPresent('back'), 'There is no "Back" button on the page');
        $this->assertTrue($this->buttonIsPresent('save_gift_registry'), 'There is no "Save User" button on the page');
        $this->assertTrue($this->buttonIsPresent('reset'), 'There is no "Reset" button on the page');
        $this->assertTrue($this->buttonIsPresent('save_and_continue_edit'), 'There is no "Reset" button on the page');
    }

    /**
     * <p>Create Gift Registry (all required fields are filled).</p>
     * <p>Steps:</p>
     * <p>1.Go to Customers - Gift Registry.</p>
     * <p>2.Press "Add New Gift Registry Type" button.</p>
     * <p>3.Fill all required fields.</p>
     * <p>4.Press "Save" button.</p>
     * <p>Expected result:</p>
     * <p>New gift registry successfully saved.</p>
     * <p>Message "The gift registry type has been saved.." is displayed.</p>
     *
     * @return array
     * @test
     * @depends navigationTest
     * @TestlinkId TL-MAGE-5871
     */
    public function withRequiredFieldsOnly()
    {
        //Data
        $giftRegistryData = $this->loadDataSet('GiftRegistry', 'new_gift_registry');
        //Steps
        $this->giftRegistryHelper()->createGiftRegistry($giftRegistryData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_gift_registry');

        return $giftRegistryData;
    }

    /**
     * <p>Validation of empty required fields in Attributes<</p>
     * <p>Steps:</p>
     * <p>1.Go to Customers - Gift Registry.</p>
     * <p>2.Press "Add New Gift Registry Type" button.</p>
     * <p>3.Fill all required fields.<</p>
     * <p>4.Add new attribute.<</p>
     * <p>5.Leave one of required field empty.<</p>
     * <p>6.Press "Save" button.</p>
     * <p>Expected result:Validation message appears</p>
     * <p>New gift registry has not been saved.</p>
     *
     * @param string $optionName
     * @param string $emptyField
     * @param string $fieldType
     *
     * @test
     * @depends navigationTest
     * @dataProvider withRequiredEmptyDataProviderAttributes
     * @TestlinkId TL-MAGE-5872
     */
    public function withRequiredFieldsEmptyAttributes($optionName, $emptyField, $fieldType)
    {
        //Data
        $option = $this->loadDataSet('GiftRegistry', $optionName, array($emptyField => '%noValue%'));
        $giftRegistryData = $this->loadDataSet('GiftRegistry', 'new_gift_registry', array('attribute_one' => $option));
        //Steps
        $this->giftRegistryHelper()->createGiftRegistry($giftRegistryData);
        //Verifying
        $this->addFieldIdToMessage($fieldType, $emptyField);
        $this->assertMessagePresent('validation', 'empty_required_field');
        $this->assertTrue($this->verifyMessagesCount(), $this->getParsedMessages());
    }

    public function withRequiredEmptyDataProviderAttributes()
    {
        return array(
            array('attributes_text', 'attributes_general_code', 'field'),
            array('attributes_text', 'attributes_general_label', 'field'),
            array('attributes_text', 'attributes_general_input_type', 'dropdown'),
            array('attributes_text', 'attributes_general_attribute_group', 'dropdown'),
            array('attributes_select', 'attributes_general_code', 'field'),
            array('attributes_select', 'attributes_general_label', 'field'),
            array('attributes_select', 'attributes_general_attribute_group', 'dropdown'),
            array('attributes_select', 'attributes_code', 'field'),
            array('attributes_select', 'attributes_label', 'field'),
            array('attributes_date', 'attributes_general_code', 'field'),
            array('attributes_date', 'attributes_general_label', 'field'),
            array('attributes_date', 'attributes_general_attribute_group', 'dropdown'),
            array('attributes_country', 'attributes_general_code', 'field'),
            array('attributes_country', 'attributes_general_label', 'field'),
            array('attributes_country', 'attributes_general_attribute_group', 'dropdown'),
            array('attributes_event_date', 'attributes_general_label', 'field'),
            array('attributes_event_country', 'attributes_general_label', 'field'),
            array('attributes_event_location', 'attributes_general_label', 'field'),
            array('attributes_role', 'attributes_general_label', 'field')
        );
    }

    /**
     * <p>Validation of incorrect code fields in Attributes<</p>
     * <p>Steps:</p>
     * <p>1.Go to Customers - Gift Registry.</p>
     * <p>2.Press "Add New Gift Registry Type" button.</p>
     * <p>3.Fill all required fields.<</p>
     * <p>4.Add new attribute.<</p>
     * <p>5.Input incorrect code.<</p>
     * <p>6.Press "Save" button.</p>
     * <p>Expected result:Validation message appears</p>
     * <p>New gift registry has not been saved.</p>
     *
     * @param string $incorrectCode
     * @param string $optionName
     * @param string $emptyField
     * @param string $fieldType
     *
     * @test
     * @depends navigationTest
     * @dataProvider withIncorrectAttributesCodeDataProvider
     * @TestlinkId TL-MAGE-5873
     */
    public function withIncorrectAttributesCode($incorrectCode, $optionName, $emptyField, $fieldType)
    {
        //Data
        $option = $this->loadDataSet('GiftRegistry', $optionName, array($emptyField => $incorrectCode));
        $giftRegistryData = $this->loadDataSet('GiftRegistry', 'new_gift_registry', array('attribute_one' => $option));
        //Steps
        $this->giftRegistryHelper()->createGiftRegistry($giftRegistryData);
        //Verifying
        $this->addFieldIdToMessage($fieldType, $emptyField);
        $this->assertMessagePresent('validation', 'incorrect_attributes_code');
        $this->assertTrue($this->verifyMessagesCount(), $this->getParsedMessages());
    }

    public function withIncorrectAttributesCodeDataProvider()
    {
        return array(
            array('event_date', 'attributes_text', 'attributes_general_code', 'field'),
            array('event_country', 'attributes_text', 'attributes_general_code', 'field'),
            array('event_location', 'attributes_text', 'attributes_general_code', 'field'),
            array('role', 'attributes_text', 'attributes_general_code', 'field'),
            array('event_date', 'attributes_select', 'attributes_general_code', 'field'),
            array('event_country', 'attributes_select', 'attributes_general_code', 'field'),
            array('event_location', 'attributes_select', 'attributes_general_code', 'field'),
            array('role', 'attributes_select', 'attributes_general_code', 'field'),
            array('event_date', 'attributes_date', 'attributes_general_code', 'field'),
            array('event_country', 'attributes_date', 'attributes_general_code', 'field'),
            array('event_location', 'attributes_date', 'attributes_general_code', 'field'),
            array('role', 'attributes_date', 'attributes_general_code', 'field'),
            array('event_date', 'attributes_country', 'attributes_general_code', 'field'),
            array('event_country', 'attributes_country', 'attributes_general_code', 'field'),
            array('event_location', 'attributes_country', 'attributes_general_code', 'field'),
            array('role', 'attributes_country', 'attributes_general_code', 'field')
        );
    }

    /**
     * <p>Validation of empty required fields</p>
     * <p>Steps:</p>
     * <p>1.Go to Customers - Gift Registry.</p>
     * <p>2.Press "Add New Gift Registry Type" button.</p>
     * <p>3.Leave empty one of required fields.<</p>
     * <p>4.Press "Save" button.</p>
     * <p>Expected result:Validation message appears</p>
     * <p>New gift registry has not been saved.</p>
     *
     * @param string $emptyField
     * @param string $fieldType
     *
     * @test
     * @depends navigationTest
     * @dataProvider withRequiredEmptyDataProvider
     * @TestlinkId TL-MAGE-5874
     */
    public function withRequiredFieldsEmpty($emptyField, $fieldType)
    {
        //Data
        $overrideData = array($emptyField => '%noValue%');
        $giftRegistryData = $this->loadDataSet('GiftRegistry', 'gift_registry_all_fields', $overrideData);
        //Steps
        $this->giftRegistryHelper()->createGiftRegistry($giftRegistryData);
        //Verifying
        $this->addFieldIdToMessage($fieldType, $emptyField);
        $this->assertMessagePresent('validation', 'empty_required_field');
        $this->assertTrue($this->verifyMessagesCount(), $this->getParsedMessages());
    }

    public function withRequiredEmptyDataProvider()
    {
        return array(
            array('code', 'field'),
            array('label', 'field')
        );
    }

    /**
     * <p>Validation of label field</p>
     * <p>Steps</p>
     * <p>1. Click "Add New Gift Registry Type".</p>
     * <p>2. Fill label field with incorrect data.</p>
     * <p>3. Press "Save" button.</p>
     * <p>Expected result: Validation message appears.</p>
     * <p>New gift registry has not been saved.</p>
     *
     * @param array $incorrectValue
     *
     * @test
     * @depends navigationTest
     * @dataProvider withIncorrectLabelDataProvider
     * @TestlinkId TL-MAGE-5875
     */
    public function withIncorrectLabel(array $incorrectValue)
    {
        //Data
        $giftRegistryData = $this->loadDataSet('GiftRegistry', 'new_gift_registry', $incorrectValue);
        //Steps
        $this->giftRegistryHelper()->createGiftRegistry($giftRegistryData);
        //Verification
        $this->assertMessagePresent('validation', 'invalid_code');
    }

    public function withIncorrectLabelDataProvider()
    {
        return array(
            array(array('code' => '1code')),
            array(array('code' => 'Code')),
            array(array('code' => 'co de')),
            array(array('code' => '#code'))
        );
    }

    /**
     * <p>Creation with existing code</p>
     * <p>Steps</p>
     * <p>1. Click "Add New Gift Registry Type".</p>
     * <p>2. Input "code" that already exist in code field. </p>
     * <p>3. Press "Save" button.</p>     *
     * <p>Expected result:</p>
     * <p>New gift registry successfully saved.</p>
     * <p>Message "The gift registry type has been saved.." is displayed.</p>
     *
     * @param array $giftRegistryData
     *
     * @test
     * @depends withRequiredFieldsOnly
     * @TestlinkId TL-MAGE-5877
     */
    public function withExistingCode($giftRegistryData)
    {
        //Data
        $giftRegistryData = $this->loadDataSet('GiftRegistry', 'new_gift_registry',
            array('code' => $giftRegistryData['general']['code']));
        //Steps
        $this->giftRegistryHelper()->createGiftRegistry($giftRegistryData);
        //Verification
        $this->assertMessagePresent('success', 'success_saved_gift_registry');
    }

    /**
     * <p>Create Gift Registry (all required fields are filled by special characters).</p>
     * <p>Steps:</p>
     * <p>1. Click "Add New Gift Registry Type".</p>
     * <p>2.Fill in all required fields by special characters.</p>
     * <p>3.Press "Save" button.</p>
     * <p>New gift registry successfully saved.</p>
     * <p>Message "The gift registry type has been saved.." is displayed.</p>
     *
     * @test
     * @depends withRequiredFieldsOnly
     * @TestlinkId TL-MAGE-5880
     */
    public function withSpecialCharactersExceptCode()
    {
        //Data
        $specialCharacters = array('label'  => $this->generate('string', 32, ':punct:'),
            'sort_order'  => $this->generate('string', 32, ':punct:'),);
        $giftRegistryData = $this->loadDataSet('GiftRegistry', 'new_gift_registry', $specialCharacters);
        //Steps
        $this->giftRegistryHelper()->createGiftRegistry($giftRegistryData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_gift_registry');
    }

    /**
     * <p>Validation of empty option in Attributes<</p>
     * <p>Steps:</p>
     * <p>1.Go to Customers - Gift Registry.</p>
     * <p>2.Press "Add New Gift Registry Type" button.</p>
     * <p>3.Fill all required fields.<</p>
     * <p>4.Add select attribute.<</p>
     * <p>5.Add empty option.<</p>
     * <p>6.Press "Save" button.</p>
     * <p>Expected result:Validation message appears</p>
     * <p>New gift registry has not been saved.</p>
     *
     * @test
     * @depends navigationTest
     * @TestlinkId TL-MAGE-5883
     */
    public function withEmptyOption()
    {
        //Data
        $option = $this->loadDataSet('GiftRegistry', 'attributes_select_empty_option');
        $giftRegistryData = $this->loadDataSet('GiftRegistry', 'new_gift_registry', array('attribute_one' => $option));
        //Steps
        $this->giftRegistryHelper()->createGiftRegistry($giftRegistryData);
        //Verifying
        $this->assertMessagePresent('validation', 'empty_option');
        $this->assertTrue($this->verifyMessagesCount(), $this->getParsedMessages());
    }

    /**
     * <p>Create Gift Registry (all required fields are filled by long value data).</p>
     * <p>Steps:</p>
     * <p>1.Click "Add New Gift Registry Type"</p>
     * <p>2.Fill in all required fields by special characters</p>
     * <p>3.Press "Save" button.</p>
     * <p>New gift registry successfully saved.</p>
     * <p>Message "The gift registry type has been saved.." is displayed.</p>
     *
     * @test
     * @depends navigationTest
     * @TestlinkId TL-MAGE-5885
     */
    public function withLongValues()
    {
        //Data
        $longValues = array('code' => $this->generate('string', 128, ':lower:'),
            'label' => $this->generate('string', 128, ':alnum:'),
            'sort_order' => $this->generate('string', 128, ':digit:'));
        $giftRegistryData = $this->loadDataSet('GiftRegistry', 'new_gift_registry', $longValues);
        //Steps
        $this->giftRegistryHelper()->createGiftRegistry($giftRegistryData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_gift_registry');
    }


    /**
     * <p>Create Gift Registry (all fields are filled).</p>
     * <p>Steps:</p>
     * <p>1.Go to Customers - Gift Registry.</p>
     * <p>2.Press "Add New Gift Registry Type" button.</p>
     * <p>3.Fill all fields.</p>
     * <p>4.Press "Save" button.</p>
     * <p>Expected result:</p>
     * <p>New gift registry successfully saved.</p>
     * <p>Message "The gift registry type has been saved.." is displayed.</p>
     *
     * @test
     * @depends navigationTest
     * @TestlinkId TL-MAGE-5886
     */
    public function withAllFields()
    {
        //Data
        $giftRegistryData = $this->loadDataSet('GiftRegistry', 'gift_registry_all_fields');
        //Steps
        $this->giftRegistryHelper()->createGiftRegistry($giftRegistryData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_gift_registry');
    }
}