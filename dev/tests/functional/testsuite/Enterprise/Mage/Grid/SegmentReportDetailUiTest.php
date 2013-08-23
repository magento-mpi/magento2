<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Enterprise_Mage_Grid_SegmentReportDetailUiTest extends Mage_Selenium_TestCase
{
    /**
     * <p>Assert Pre Conditions</p>
     * <p>1. Log in to backend</p>
     */
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
    }

    /**
     * <p>Need to verify that all grid elements are presented on page</p>
     *
     * @test
     */
    public function uiElementsSegmentDetailsGridTest()
    {
        $this->navigate('manage_customer_segments');
        $segmentData = $this->loadDataSet('CustomerSegment', 'segment_with_all_fields');
        $this->customerSegmentHelper()->createSegment($segmentData);
        $this->navigate('customer_segment_report');
        $segmentSearch = array('segment_name' =>$segmentData['general_properties']['segment_name']);
        $this->searchAndOpen($segmentSearch, 'customer_segment_report_grid');
        //Data
        $testData = $this->loadDataSet('UiElements', 'customer_segment_report_detail');
        $this->gridHelper()->prepareData($testData);
        //Verification
        $this->assertEmptyVerificationErrors();
        $actualHeadersName = $this->gridHelper()->getGridHeaders($testData);
        $expectedHeadersName = $testData['headers'];
        //Verification
        $this->assertEquals($expectedHeadersName, $actualHeadersName,
            "Header names are not equal on customer_segment_report_detail page");
    }
}
