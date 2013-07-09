<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_GiftRegistry
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Gift Registry creation into backend
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Enterprise_Mage_GiftRegistry_CreateTest extends Mage_Selenium_TestCase
{
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->navigate('manage_gift_registry');
    }

    /**
     * <p>Navigation test.</p>
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
        $this->assertTrue($this->buttonIsPresent('save_gift_registry'), 'There is no "Save" button on the page');
        $this->assertTrue($this->buttonIsPresent('reset'), 'There is no "Reset" button on the page');
        $this->assertTrue($this->buttonIsPresent('save_and_continue_edit'), 'There is no "Reset" button on the page');
    }

    /**
     * <p>Create Gift Registry (all required fields are filled).</p>
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
        $this->addFieldIdToMessage('field', 'code');
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
     *
     * @test
     * @depends withRequiredFieldsOnly
     * @TestlinkId TL-MAGE-5880
     */
    public function withSpecialCharactersExceptCode()
    {
        //Data
        $specialCharacters = array('label' => $this->generate('string', 32, ':punct:'),
            'sort_order' => $this->generate('string', 32, ':punct:'),);
        $giftRegistryData = $this->loadDataSet('GiftRegistry', 'new_gift_registry', $specialCharacters);
        //Steps
        $this->giftRegistryHelper()->createGiftRegistry($giftRegistryData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_gift_registry');
    }

    /**
     * <p>Validation of empty option in Attributes<</p>
     *
     * @test
     * @depends navigationTest
     * @TestlinkId TL-MAGE-5883
     */
    public function withEmptyOption()
    {
        $this->markTestIncomplete('Bug MAGETWO-8067');
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