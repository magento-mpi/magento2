<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Tags
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
require_once 'TagsFixtureAbstract.php';
/**
 * Tag Reports
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Community2_Mage_Tags_ReportsTest extends Community2_Mage_Tags_TagsFixtureAbstract
{
    protected function tearDownAfterTest()
    {
        $this->loginAdminUser();
        $this->navigate('all_tags');
        $this->tagsHelper()->deleteAllTags();
        $this->logoutCustomer();
    }

    /**
     * @return array
     * @test
     * @skipTearDown
     */
    public function preconditionsForReportEntriesTest()
    {
        return parent::_preconditionsForReportEntriesTest();
    }
    /**
     * Checking entries in the tags reports
     * Preconditions:
     * 1. Created and assigned products to the store view.
     * 2. Logged in customer that didn't create any tags.
     * Steps:
     * 1. Open Frontend.
     * 2. Go to the Product view page.
     * 3. Enter new tag name to the "Add Your Tags:" field.
     * 4. Click on "Add Tags" button.
     * Expected:
     * System should reply with message “1 tag(s) have been accepted for moderation.”
     * Added tag is shown in the My Account -> My Tags.
     * Tag is added to the Tags -> All Tags.
     * Tag received "Pending" status.
     * Tagged product is shown in "Products Tagged by Customers" on the Tag page.
     * 5. Log into Backend.
     * 6. Go to the Catalog -> Tags -> Pending Tags.
     * 7. Select your tag and approve it.
     * 8. Refresh Cache.
     * 9. Go to the Reports -> Tags -> Customers.
     * Expected: Entry about your customer should be added.
     * 10. Click on "Show Tags" link in your customer.
     * Expected: Information about product name, tag name, visibility on store views and submission details should be
     * displayed.
     * 11. Go to the Reports -> Tags -> Products.
     * Expected: "Number of Unique Tags" and "Number of Total Tags" should be increased by 1 for the product for what
     * was assigned new tag.
     * 12. Click on "Show Tags" link of your product.
     * Expected: New tag should be in the tag list.
     * 13. Go to the Reports -> Tags -> Popular.
     * Expected: Your tag should be in the tag list with popularity 1.
     * 14. Click on "Show Details" link near your tag.
     * Expected: Information about customer, product and store view should be displayed.
     *
     * @param array $testData
     *
     * @test
     * @author Iuliia Babenko
     * @depends preconditionsForReportEntriesTest
     * @TestlinkId TL-MAGE-2465
     */
    public function entriesInTagReports($testData)
    {
        //Step 1
        $this->customerHelper()->frontLoginCustomer(array(
            'email' => $testData['customer']['email'],
            'password' => $testData['customer']['password'])
        );
        //Step 2
        $this->productHelper()->frontOpenProduct($testData['product']);
        //Steps 3-4
        $tags = array($this->generate('string', 4, ':lower:'), $this->generate('string', 4, ':lower:'));
        foreach ($tags as $tag) {
            $this->tagsHelper()->frontendAddTag($tag);
            //Verifying
            $this->assertMessagePresent('success', 'tag_accepted_success');
        }
        $this->tagsHelper()->frontendTagVerification($tags, $testData['product']);
        $this->loginAdminUser();
        foreach ($tags as $tag) {
            $this->navigate('all_tags');
            $this->tagsHelper()->verifyTag(array('tag_name' => $tag, 'status' => 'Pending'));
        }
        //Steps 5-6
        $this->navigate('pending_tags');
        //Step 7
        foreach ($tags as $tag) {
            $this->tagsHelper()->changeTagsStatus(array(array('tag_name' => $tag)), 'Approved');
        }
        //Step 8
        $this->flushCache();
        //Step 9
        $this->navigate('report_tag_customer');
        //Verifying
        $this->assertNotNull($this->reportsHelper()->searchDataInReport(array(
            'first_name' => $testData['customer']['first_name'],
            'last_name' => $testData['customer']['last_name'],
            'total_tags' => count($tags),
        )), 'Customer who submitted tag is not shown in report');
        //Step 10
        $this->addParameter('firstName', $testData['customer']['first_name']);
        $this->addParameter('lastName', $testData['customer']['last_name']);
        $this->addParameter('customer_first_last_name', $testData['customer']['first_name']
            . ' ' . $testData['customer']['last_name']);
        $this->clickControl('link', 'show_tags');
        //Verifying
        foreach ($tags as $tag) {
            $this->assertNotNull($this->reportsHelper()->searchDataInReport(array(
                'product_name' => $testData['product'],
                'tag_name' => $tag,
            )), 'Tag is not shown in Tags Submitted by Customer');
        }
        //Step 11
        $this->navigate('report_tag_product');
        //Verifying
        $this->assertNotNull($this->reportsHelper()->searchDataInReport(array(
            'product_name' => $testData['product'],
            'unique_tags_number' => count($tags),
            'total_tags_number' => count($tags),
        )), 'Product with submitted tag is not shown in report');
        //Step 12
        $this->addParameter('productName', $testData['product']);
        $this->clickControl('link', 'show_tags');
        //Verifying
        foreach ($tags as $tag) {
            $this->assertNotNull($this->reportsHelper()->searchDataInReport(array(
                'tag_name' => $tag,
                'tag_use' => '1',
            )), 'Tag is not shown in Tags Submitted to Product');
        }
        //Step 13
        $this->navigate('report_tag_popular');
        //Verifying
        foreach ($tags as $tag) {
            $this->assertNotNull($this->reportsHelper()->searchDataInReport(array(
                'tag_name' => $tag,
                'popularity' => '1',
            )), 'Tag is not shown in report');
        }
        //Step 14
        foreach ($tags as $tag) {
            $this->navigate('report_tag_popular');
            $this->addParameter('tagName', $tag);
            $this->clickControl('link', 'show_details');
            //Verifying
            $this->assertNotNull($this->reportsHelper()->searchDataInReport(array(
                'first_name' => $testData['customer']['first_name'],
                'last_name' => $testData['customer']['last_name'],
                'product_name' => $testData['product'],
            )), 'Tag is not shown in Tag Details');
        }
    }
    /**
     * @return array
     * @test
     * @skipTearDown
     */
    public function preconditionsForReportsTests()
    {
        return parent::_preconditionsForReportsTests();
    }

    /**
     * Verifying columns and sorting in Customers Tags report
     *
     * @param array $testData
     *
     * @test
     * @author Iuliia Babenko
     * @skipTearDown
     * @depends preconditionsForReportsTests
     * @TestlinkId TL-MAGE-2459
     */
    public function customerTagsReport($testData)
    {
        $this->navigate('report_tag_customer');
        //Verifying columns
        $columns = array('id', 'first_name', 'last_name', 'total_tags', 'action');
        foreach ($columns as $column) {
            $this->assertTrue($this->isElementPresent($this->_getControlXpath('link', $column)),
                "Column $column is not present in report");
        }
        //Verifying content
        $reportContent = array(
            array(
                'first_name' => $testData[0]['customer']['first_name'],
                'last_name' => $testData[0]['customer']['last_name'],
                'total_tags' => count($testData[0]['tags']),
            ),
            array(
                'first_name' => $testData[1]['customer']['first_name'],
                'last_name' => $testData[1]['customer']['last_name'],
                'total_tags' => count($testData[1]['tags']),
            ),
        );

        foreach ($reportContent as $reportRow) {
            $this->assertNotNull($this->reportsHelper()->searchDataInReport($reportRow),
                'Customer who submitted tag is not shown in report');
        }
        //Verifying columns sorting
        $sortedColumns = array('first_name', 'last_name', 'total_tags');
        foreach ($sortedColumns as $column) {
            $this->clickControl('link', $column, false);
            sleep(3);
            $this->reportsHelper()->verifyReportSortingByColumn($reportContent, $column);
        }
    }
    /**
     * Verifying columns and sorting in Products Tags report
     *
     * @param array $testData
     *
     * @test
     * @author Iuliia Babenko
     * @skipTearDown
     * @depends preconditionsForReportsTests
     * @TestlinkId TL-MAGE-2461
     */
    public function productTagsReport($testData)
    {
        $this->navigate('report_tag_product');
        //Verifying columns
        $columns = array('id', 'product_name', 'unique_tags_number', 'total_tags_number', 'action');
        foreach ($columns as $column) {
            $this->assertTrue($this->isElementPresent($this->_getControlXpath('link', $column)),
                "Column $column is not present in report");
        }
        //Verifying content
        $reportContent = array(
            array(
                'product_name' => $testData[0]['product'],
                'unique_tags_number' => count($testData[0]['tags']),
                'total_tags_number' => count($testData[0]['tags']),
            ),
            array(
                'product_name' => $testData[1]['product'],
                'unique_tags_number' => count($testData[1]['tags']),
                'total_tags_number' => count($testData[1]['tags']),
            ),
        );

        foreach ($reportContent as $reportRow) {
            $this->assertNotNull($this->reportsHelper()->searchDataInReport($reportRow),
                'Product with submitted tag is not shown in report');
        }
        //Verifying columns sorting
        $sortedColumns = array('product_name', 'unique_tags_number', 'total_tags_number');
        foreach ($sortedColumns as $column) {
            $this->clickControl('link', $column, false);
            sleep(3);
            $this->reportsHelper()->verifyReportSortingByColumn($reportContent, $column);
        }
    }
    /**
     * Verifying columns and sorting in Popular Tags report
     *
     * @param array $testData
     *
     * @test
     * @author Iuliia Babenko
     * @skipTearDown
     * @depends preconditionsForReportsTests
     * @TestlinkId TL-MAGE-2463
     */
    public function popularTagsReport($testData)
    {
        $this->navigate('report_tag_popular');
        //Verifying columns
        $columns = array('tag_name', 'popularity', 'action');
        foreach ($columns as $column) {
            $this->assertTrue($this->isElementPresent($this->_getControlXpath('link', $column)),
                "Column $column is not present in report");
        }
        //Verifying content
        $reportContent = array(
            array(
                'tag_name' => $testData[0]['tags'][0],
                'popularity' => '1',
            ),
            array(
                'tag_name' => $testData[1]['tags'][0],
                'popularity' => '1',
            ),
            array(
                'tag_name' => $testData[1]['tags'][1],
                'popularity' => '1',
            ),
        );

        foreach ($reportContent as $reportRow) {
            $this->assertNotNull($this->reportsHelper()->searchDataInReport($reportRow),
                'Tag is not shown in report');
        }
        //Verifying columns sorting
        $sortedColumns = array('tag_name');
        foreach ($sortedColumns as $column) {
            $this->clickControl('link', $column, false);
            sleep(3);
            $this->reportsHelper()->verifyReportSortingByColumn($reportContent, $column);
        }
    }
    /**
     * Verifying that Customers Tags report can be exported to a CSV or an Excel file.
     * Steps:
     * 1. Go to the Reports -> Tags -> Customers.
     * 2. Select Export to: "CSV" and click on "Export" button. Select path and click on "OK" button.
     * Expected: File should contain all entries from the grid.
     * 3. Select Export to: "Excel XML" and click on "Export" button. Select path and click on "OK" button.
     * Expected: File should contain all entries from the grid.
     * 4. Click on "Show Tags" link.
     * 5. Select Export to: "CSV" and click on "Export" button. Select path and click on "OK" button.
     * Expected: File should contain all entries from the grid.
     * 6. Select Export to: "Excel XML" and click on "Export" button. Select path and click on "OK" button.
     * Expected: File should contain all entries from the grid.
     *
     * @param array $testData
     *
     * @test
     * @author Iuliia Babenko
     * @skipTearDown
     * @depends preconditionsForReportsTests
     * @TestlinkId TL-MAGE-2460
     */
    public function exportCustomerTagsReport($testData)
    {
        //Step 1
        $this->navigate('report_tag_customer');
        //Step 2
        $this->fillDropdown('export_to', 'CSV');
        $exportedReportCsv = $this->reportsHelper()->export();
        //Verifying
        $gridReport = array(
            array(
                'First Name' => $testData[0]['customer']['first_name'],
                'Last Name' => $testData[0]['customer']['last_name'],
                'Total Tags' => count($testData[0]['tags']),
            ),
            array(
                'First Name' => $testData[1]['customer']['first_name'],
                'Last Name' => $testData[1]['customer']['last_name'],
                'Total Tags' => count($testData[1]['tags']),
            ),
        );
        $this->reportsHelper()->verifyExportedReport($gridReport, $exportedReportCsv);
        //Step 3
        $this->fillDropdown('export_to', 'Excel XML');
        $exportedReportXml = $this->reportsHelper()->export();
        //Verifying
        $this->reportsHelper()->verifyExportedReport($gridReport, $exportedReportXml);
        //Step 4
        foreach ($testData as $dataRow) {
            $this->addParameter('firstName', $dataRow['customer']['first_name']);
            $this->addParameter('lastName', $dataRow['customer']['last_name']);
            $this->addParameter('customer_first_last_name', $dataRow['customer']['first_name']
                . ' ' . $dataRow['customer']['last_name']);
            $this->clickControl('link', 'show_tags');
            //Step 5
            $this->fillDropdown('export_to', 'CSV');
            $showTagReportCsv = $this->reportsHelper()->export();
            //Verifying
            $showTagReport = array();
            foreach ($dataRow['tags'] as $tag) {
                $showTagReport[] = array(
                    'Product Name' => $dataRow['product'],
                    'Tag Name' => $tag,
                );
            }
            $this->reportsHelper()->verifyExportedReport($showTagReport, $showTagReportCsv);
            //Step 6
            $this->fillDropdown('export_to', 'Excel XML');
            $showTagReportXML = $this->reportsHelper()->export();
            //Verifying
            $this->reportsHelper()->verifyExportedReport($showTagReport, $showTagReportXML);
            $this->clickButton('back');
        }
    }
    /**
     * Verifying that Products Tags report can be exported to a CSV or an Excel file.
     * Steps:
     * 1. Go to the Reports -> Tags -> Customers.
     * 2. Select Export to: "CSV" and click on "Export" button. Select path and click on "OK" button.
     * Expected: File should contain all entries from the grid.
     * 3. Select Export to: "Excel XML" and click on "Export" button. Select path and click on "OK" button.
     * Expected: File should contain all entries from the grid.
     * 4. Click on "Show Tags" link.
     * 5. Select Export to: "CSV" and click on "Export" button. Select path and click on "OK" button.
     * Expected: File should contain all entries from the grid.
     * 6. Select Export to: "Excel XML" and click on "Export" button. Select path and click on "OK" button.
     * Expected: File should contain all entries from the grid.
     *
     * @param array $testData
     *
     * @test
     * @author Iuliia Babenko
     * @skipTearDown
     * @depends preconditionsForReportsTests
     * @TestlinkId TL-MAGE-2462
     */
    public function exportProductTagsReport($testData)
    {
        //Step 1
        $this->navigate('report_tag_product');
        //Step 2
        $this->fillDropdown('export_to', 'CSV');
        $exportedReportCsv = $this->reportsHelper()->export();
        //Verifying
        $gridReport = array(
            array(
                'Product Name' => $testData[0]['product'],
                'Number of Unique Tags' => count($testData[0]['tags']),
                'Number of Total Tags' => count($testData[0]['tags']),
            ),
            array(
                'Product Name' => $testData[1]['product'],
                'Number of Unique Tags' => count($testData[1]['tags']),
                'Number of Total Tags' => count($testData[1]['tags']),
            ),
        );
        $this->reportsHelper()->verifyExportedReport($gridReport, $exportedReportCsv);
        //Step 3
        $this->fillDropdown('export_to', 'Excel XML');
        $exportedReportXml = $this->reportsHelper()->export();
        //Verifying
        $this->reportsHelper()->verifyExportedReport($gridReport, $exportedReportXml);
        //Step 4
        foreach ($testData as $dataRow) {
            $this->addParameter('productName', $dataRow['product']);
            $this->clickControl('link', 'show_tags');
            //Step 5
            $this->fillDropdown('export_to', 'CSV');
            $showTagReportCsv = $this->reportsHelper()->export();
            //Verifying
            $showTagReport = array();
            foreach ($dataRow['tags'] as $tag) {
                $showTagReport[] = array(
                    'Tag Name' => $tag,
                    'Tag Use' => '1',
                );
            }
            $this->reportsHelper()->verifyExportedReport($showTagReport, $showTagReportCsv);
            //Step 6
            $this->fillDropdown('export_to', 'Excel XML');
            $showTagReportXML = $this->reportsHelper()->export();
            //Verifying
            $this->reportsHelper()->verifyExportedReport($showTagReport, $showTagReportXML);
            $this->clickButton('back');
        }
    }
    /**
     * Verifying that Popular Tags report can be exported to a CSV or an Excel file.
     * Steps:
     * 1. Go to the Reports -> Tags -> Customers.
     * 2. Select Export to: "CSV" and click on "Export" button. Select path and click on "OK" button.
     * Expected: File should contain all entries from the grid.
     * 3. Select Export to: "Excel XML" and click on "Export" button. Select path and click on "OK" button.
     * Expected: File should contain all entries from the grid.
     * 4. Click on "Show Tags" link.
     * 5. Select Export to: "CSV" and click on "Export" button. Select path and click on "OK" button.
     * Expected: File should contain all entries from the grid.
     * 6. Select Export to: "Excel XML" and click on "Export" button. Select path and click on "OK" button.
     * Expected: File should contain all entries from the grid.
     *
     * @param array $testData
     *
     * @test
     * @author Iuliia Babenko
     * @depends preconditionsForReportsTests
     * @TestlinkId TL-MAGE-2464
     */
    public function exportPopularTagsReport($testData)
    {
        //Step 1
        $this->navigate('report_tag_popular');
        //Step 2
        $this->fillDropdown('export_to', 'CSV');
        $exportedReportCsv = $this->reportsHelper()->export();
        //Verifying
        $gridReport = array();
        foreach ($testData as $key => $testDataValue) {
            foreach ($testData[$key]['tags'] as $tag) {
                $gridReport[] = array(
                    'Tag Name' => $tag,
                    'Popularity' => '1',
                );
            }
        }
        $this->reportsHelper()->verifyExportedReport($gridReport, $exportedReportCsv);
        //Step 3
        $this->fillDropdown('export_to', 'Excel XML');
        $exportedReportXml = $this->reportsHelper()->export();
        //Verifying
        $this->reportsHelper()->verifyExportedReport($gridReport, $exportedReportXml);
        //Step 4
        foreach ($testData as $key => $testDataValue) {
            foreach ($testData[$key]['tags'] as $tag) {
                $this->addParameter('tagName', $tag);
                $this->clickControl('link', 'show_details');
                //Step 5
                $this->fillDropdown('export_to', 'CSV');
                $showDetailsReportCsv = $this->reportsHelper()->export();
                //Verifying
                $showDetailsReport[0] = array(
                    'First Name' => $testData[$key]['customer']['first_name'],
                    'Last Name' => $testData[$key]['customer']['last_name'],
                    'Product Name' => $testData[$key]['product'],
                );
                $this->reportsHelper()->verifyExportedReport($showDetailsReport, $showDetailsReportCsv);
                //Step 6
                $this->fillDropdown('export_to', 'Excel XML');
                $showDetailsReportXML = $this->reportsHelper()->export();
                //Verifying
                $this->reportsHelper()->verifyExportedReport($showDetailsReport, $showDetailsReportXML);
                $this->clickButton('back');
            }
        }
    }
}