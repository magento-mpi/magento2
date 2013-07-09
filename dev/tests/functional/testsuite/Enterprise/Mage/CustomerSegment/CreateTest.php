<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_CustomerSegment
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Creating Customer Segments with correct and incorrect data
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Enterprise_Mage_CustomerSegment_CreateTest extends Mage_Selenium_TestCase
{
    protected function assertPreconditions()
    {
        $this->loginAdminUser();
    }

    /**
     * <p>Preconditions:</p>
     * <p>Log in to Backend.</p>
     */
    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('CustomerSegment/enable_customer_segment');
        $this->systemConfigurationHelper()->configure('SingleStoreMode/disable_single_store_mode');
    }

    /**
     * @param string $fieldName
     * @param string $fieldType
     *
     * @test
     * @dataProvider withRequiredFieldsEmptyDataProvider
     * @TestlinkId TL-MAGE-1827
     */
    public function withRequiredFieldsEmpty($fieldName, $fieldType)
    {
        //Data
        $dataToOverride = array();
        if ($fieldType == 'multiselect') {
            $dataToOverride[$fieldName] = '%noValue%';
        } else {
            $dataToOverride[$fieldName] = '';
        }
        $segmentData = $this->loadDataSet('CustomerSegment', 'segment_with_all_fields', $dataToOverride);
        //Steps
        $this->navigate('manage_customer_segments');
        $this->customerSegmentHelper()->createSegment($segmentData);
        //Verification
        $this->addFieldIdToMessage($fieldType, $fieldName);
        $this->assertMessagePresent('validation', 'empty_required_field');
        $this->assertTrue($this->verifyMessagesCount(), $this->getParsedMessages());
    }

    public function withRequiredFieldsEmptyDataProvider()
    {
        return array(
            array('segment_name', 'field'),
            array('assigned_to_website', 'multiselect'),
        );
    }

    /**
     * @test
     * @TestlinkId TL-MAGE-1827
     */
    public function createWithRequiredFields()
    {
        //Data
        $segmentData = $this->loadDataSet('CustomerSegment', 'segment_with_required_fields');
        //Steps
        $this->navigate('manage_customer_segments');
        $this->customerSegmentHelper()->createSegment($segmentData);
        //Verification
        $this->assertMessagePresent('success', 'success_saved_segment');
    }

    /**
     * @test
     * @TestlinkId TL-MAGE-1827
     */
    public function createWithAllFields()
    {
        //Data
        $segmentData = $this->loadDataSet('CustomerSegment', 'segment_with_all_fields');
        //Steps
        $this->navigate('manage_customer_segments');
        $this->customerSegmentHelper()->createSegment($segmentData);
        //Verification
        $this->assertMessagePresent('success', 'success_saved_segment');
    }

    /**
     * @param string $fieldName
     *
     * @test
     * @dataProvider createWithSpecialSymbolsDataProvider
     * @TestlinkId TL-MAGE-1827
     */

    public function createWithLongValues($fieldName)
    {
        //Data
        $segmentData = $this->loadDataSet('CustomerSegment', 'segment_with_all_fields',
            array($fieldName => $this->generate('string', 255, ':alnum:')));
        //Steps
        $this->navigate('manage_customer_segments');
        $this->customerSegmentHelper()->createSegment($segmentData);
        //Verification
        $this->assertMessagePresent('success', 'success_saved_segment');
    }

    /**
     * @param string $fieldName
     *
     * @test
     * @dataProvider createWithSpecialSymbolsDataProvider
     * @TestlinkId TL-MAGE-1827
     */

    public function createWithSpecialSymbols($fieldName)
    {
        //Data
        $segmentData = $this->loadDataSet('CustomerSegment', 'segment_with_all_fields',
            array($fieldName => $this->generate('string', 255, ':punct:')));
        //Steps
        $this->navigate('manage_customer_segments');
        $this->customerSegmentHelper()->createSegment($segmentData);
        //Verification
        $this->assertMessagePresent('success', 'success_saved_segment');
    }

    public function createWithSpecialSymbolsDataProvider()
    {
        return array(
            array('segment_name'),
            array('description'),
        );
    }
}
