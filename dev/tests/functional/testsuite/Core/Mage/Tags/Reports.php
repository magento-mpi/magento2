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
/**
 * Tag Reports
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_Tags_ReportsTest extends Core_Mage_Tags_TagsFixtureAbstract
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
     *
     * @param array $testData
     *
     * @test
     * @depends preconditionsForReportEntriesTest
     * @TestlinkId TL-MAGE-2465
     */
    public function entriesInTagReports($testData)
    {
        $this->markTestIncomplete('MAGETWO-1299');
        //Step 1
        $this->customerHelper()->frontLoginCustomer(array('email' => $testData['customer']['email'],
            'password' => $testData['customer']['password']));
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
            $this->tagsHelper()->verifyTag(array('tag_name' => $tag, 'tags_status' => 'Pending'));
        }
        //Steps 5-6
        $this->navigate('all_tags');
        //Step 7
        foreach ($tags as $tag) {
            $this->tagsHelper()->changeTagsStatus(array(array('tag_name' => $tag)), 'Approved');
        }
        //Step 8
        $this->flushCache();
        //Step 9
        $this->navigate('report_tag_customer');
        //Verifying
        $searchData = array(
            'first_name' => $testData['customer']['first_name'],
            'last_name' => $testData['customer']['last_name'],
            'total_tags' => count($tags)
        );
        $this->assertNotEmpty(
            $this->reportsHelper()->searchDataInReport($searchData),
            'Customer who submitted tag is not shown in report. Data: ' . print_r($searchData, true)
        );
        //Step 10
        $this->addParameter('firstName', $testData['customer']['first_name']);
        $this->addParameter('lastName', $testData['customer']['last_name']);
        $this->addParameter('elementTitle', $testData['customer']['first_name'] . ' ' .
            $testData['customer']['last_name']);
        $this->clickControl('link', 'show_tags');
        //Verifying
        foreach ($tags as $tag) {
            $searchData = array('product_name' => $testData['product'], 'tag_name' => $tag);
            $this->assertNotEmpty(
                $this->reportsHelper()->searchDataInReport($searchData),
                'Tag is not shown in Tags Submitted by Customer. Data: ' . print_r($searchData, true)
            );
        }
        //Step 11
        $this->navigate('report_tag_product');
        //Verifying
        $searchData = array(
            'product_name' => $testData['product'],
            'unique_tags_number' => count($tags),
            'total_tags_number' => count($tags)
        );
        $this->assertNotEmpty(
            $this->reportsHelper()->searchDataInReport($searchData),
            'Product with submitted tag is not shown in report. Data: ' . print_r($searchData, true)
        );
        //Step 12
        $this->addParameter('productName', $testData['product']);
        $this->addParameter('elementTitle', $testData['product']);
        $this->clickControl('link', 'show_tags');
        //Verifying
        foreach ($tags as $tag) {
            $searchData = array('tag_name' => $tag, 'tag_use' => '1');
            $this->assertNotEmpty(
                $this->reportsHelper()->searchDataInReport($searchData),
                'Tag is not shown in Tags Submitted to Product. Data: ' . print_r($searchData, true)
            );
        }
        //Step 13
        $this->navigate('report_tag_popular');
        //Verifying
        foreach ($tags as $tag) {
            $searchData = array('tag_name' => $tag, 'popularity' => '1');
            $this->assertNotEmpty(
                $this->reportsHelper()->searchDataInReport($searchData),
                'Tag is not shown in report. Data: ' . print_r($searchData, true)
            );
        }
        //Step 14
        foreach ($tags as $tag) {
            $this->navigate('report_tag_popular');
            $this->addParameter('tagName', $tag);
            $this->addParameter('elementTitle', $tag);
            $this->clickControl('link', 'show_details');
            //Verifying
            $searchData = array(
                'first_name' => $testData['customer']['first_name'],
                'last_name' => $testData['customer']['last_name'],
                'product_name' => $testData['product']
            );
            $this->assertNotEmpty(
                $this->reportsHelper()->searchDataInReport($searchData),
                'Tag is not shown in Tag Details. Data: ' . print_r($searchData, true)
            );
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
            $this->assertTrue($this->controlIsPresent('link', $column), "Column $column is not present in report");
        }
        //Verifying content
        $reportContent = array(
            array(
                'first_name' => $testData[0]['customer']['first_name'],
                'last_name' => $testData[0]['customer']['last_name'],
                'total_tags' => count($testData[0]['tags'])
            ),
            array(
                'first_name' => $testData[1]['customer']['first_name'],
                'last_name' => $testData[1]['customer']['last_name'],
                'total_tags' => count($testData[1]['tags'])
            )
        );
        foreach ($reportContent as $reportRow) {
            $this->assertNotEmpty(
                $this->reportsHelper()->searchDataInReport($reportRow),
                'Customer who submitted tag is not shown in report. Data: ' . print_r($reportRow, true)
            );
        }
        //Verifying columns sorting
        $sortedColumns = array('first_name', 'last_name', 'total_tags');
        foreach ($sortedColumns as $column) {
            $this->clickControl('link', $column, false);
            $this->waitForPageToLoad();
            $this->reportsHelper()->verifyReportSortingByColumn($reportContent, $column);
        }
    }

    /**
     * Verifying columns and sorting in Products Tags report
     *
     * @param array $testData
     *
     * @test
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
            $this->assertTrue($this->controlIsPresent('link', $column), "Column $column is not present in report");
        }
        //Verifying content
        $reportContent = array(
            array(
                'product_name' => $testData[0]['product'],
                'unique_tags_number' => count($testData[0]['tags']),
                'total_tags_number' => count($testData[0]['tags'])
            ),
            array(
                'product_name' => $testData[1]['product'],
                'unique_tags_number' => count($testData[1]['tags']),
                'total_tags_number' => count($testData[1]['tags'])
            )
        );
        foreach ($reportContent as $reportRow) {
            $this->assertNotEmpty(
                $this->reportsHelper()->searchDataInReport($reportRow),
                'Product with submitted tag is not shown in report. Data: ' . print_r($reportRow, true)
            );
        }
        //Verifying columns sorting
        $sortedColumns = array('product_name', 'unique_tags_number', 'total_tags_number');
        foreach ($sortedColumns as $column) {
            $this->clickControl('link', $column, false);
            $this->waitForPageToLoad();
            $this->reportsHelper()->verifyReportSortingByColumn($reportContent, $column);
        }
    }

    /**
     * Verifying columns and sorting in Popular Tags report
     *
     * @param array $testData
     *
     * @test
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
            $this->assertTrue($this->controlIsPresent('link', $column), "Column $column is not present in report");
        }
        //Verifying content
        $reportContent = array(
            array('tag_name' => $testData[0]['tags'][0], 'popularity' => '1'),
            array('tag_name' => $testData[1]['tags'][0], 'popularity' => '1'),
            array('tag_name' => $testData[1]['tags'][1], 'popularity' => '1')
        );
        foreach ($reportContent as $reportRow) {
            $this->assertNotEmpty(
                $this->reportsHelper()->searchDataInReport($reportRow),
                'Tag is not shown in report. Data: ' . print_r($reportRow, true)
            );
        }
        //Verifying columns sorting
        $sortedColumns = array('tag_name');
        foreach ($sortedColumns as $column) {
            $this->clickControl('link', $column, false);
            $this->waitForPageToLoad();
            $this->reportsHelper()->verifyReportSortingByColumn($reportContent, $column);
        }
    }

    /**
     * Verifying that Customers Tags report can be exported to a CSV or an Excel file.
     *
     * @param array $testData
     *
     * @test
     * @skipTearDown
     * @depends preconditionsForReportsTests
     * @TestlinkId TL-MAGE-2460
     */
    public function exportCustomerTagsReport($testData)
    {
        $this->markTestIncomplete('BUG: Fatal error after step5(export to csv)');
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
                'Total Tags' => count($testData[0]['tags'])
            ),
            array(
                'First Name' => $testData[1]['customer']['first_name'],
                'Last Name' => $testData[1]['customer']['last_name'],
                'Total Tags' => count($testData[1]['tags'])
            )
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
            $this->addParameter('elementTitle', $dataRow['customer']['first_name'] . ' ' .
                $dataRow['customer']['last_name']);
            $this->clickControl('link', 'show_tags');
            //Step 5
            $this->fillDropdown('export_to', 'CSV');
            $showTagReportCsv = $this->reportsHelper()->export();
            //Verifying
            $showTagReport = array();
            foreach ($dataRow['tags'] as $tag) {
                $showTagReport[] = array('Product Name' => $dataRow['product'], 'Tag Name' => $tag);
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
     *
     * @param array $testData
     *
     * @test
     * @skipTearDown
     * @depends preconditionsForReportsTests
     * @TestlinkId TL-MAGE-2462
     */
    public function exportProductTagsReport($testData)
    {
        $this->markTestIncomplete('BUG: Fatal error after step5(export to csv)');
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
                'Number of Total Tags' => count($testData[0]['tags'])
            ),
            array(
                'Product Name' => $testData[1]['product'],
                'Number of Unique Tags' => count($testData[1]['tags']),
                'Number of Total Tags' => count($testData[1]['tags'])
            )
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
            $this->addParameter('elementTitle', $dataRow['product']);
            $this->clickControl('link', 'show_tags');
            //Step 5
            $this->fillDropdown('export_to', 'CSV');
            $showTagReportCsv = $this->reportsHelper()->export();
            //Verifying
            $showTagReport = array();
            foreach ($dataRow['tags'] as $tag) {
                $showTagReport[] = array('Tag Name' => $tag, 'Tag Use' => '1');
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
     *
     * @param array $testData
     *
     * @test
     * @depends preconditionsForReportsTests
     * @TestlinkId TL-MAGE-2464
     */
    public function exportPopularTagsReport($testData)
    {
        $this->markTestIncomplete('BUG: Fatal error after step2(export to csv)');
        //Step 1
        $this->navigate('report_tag_popular');
        //Step 2
        $this->fillDropdown('export_to', 'CSV');
        $exportedReportCsv = $this->reportsHelper()->export();
        //Verifying
        $gridReport = array();
        foreach ($testData as $key => $testDataValue) {
            foreach ($testData[$key]['tags'] as $tag) {
                $gridReport[] = array('Tag Name' => $tag, 'Popularity' => '1');
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
                $this->addParameter('elementTitle', $tag);
                $this->clickControl('link', 'show_details');
                //Step 5
                $this->fillDropdown('export_to', 'CSV');
                $showDetailsReportCsv = $this->reportsHelper()->export();
                //Verifying
                $showDetailsReport[0] = array(
                    'First Name' => $testData[$key]['customer']['first_name'],
                    'Last Name' => $testData[$key]['customer']['last_name'],
                    'Product Name' => $testData[$key]['product']
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
