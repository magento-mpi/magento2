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
 * Deleting Customer Segment
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Enterprise_Mage_CustomerSegment_DeleteTest extends Mage_Selenium_TestCase
{
    /**
     * <p>Preconditions:</p>
     * <p>Log in to Backend.</p>
     */
    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('CustomerSegment/enable_customer_segment');
    }

    /**
     * @test
     * @TestlinkId TL-MAGE-1843
     */
    public function deleteCustomerSegment()
    {
        //Data
        $segmentData = $this->loadDataSet('CustomerSegment', 'segment_with_all_fields');
        $segmentSearch = array('segment_name' =>$segmentData['general_properties']['segment_name']);
        //Steps
        $this->navigate('manage_customer_segments');
        $this->customerSegmentHelper()->createSegment($segmentData);
        //Verification
        $this->assertMessagePresent('success', 'success_saved_segment');
        //Steps
        $this->customerSegmentHelper()->deleteSegment($segmentSearch);
        //Verification
        $this->assertMessagePresent('success', 'success_deleted_segment');
    }
}
