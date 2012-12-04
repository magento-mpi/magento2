<?php
/**
 * Test class Community2_Mage_XmlSitemap_CreateTest
 *
 * @copyright {}
 */
class Community2_Mage_XmlSitemap_CreateTest extends Mage_Selenium_TestCase
{
    /**
     * <p>Preconditions:</p>
     * <p>1. Login to Admin page</p>
     */
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
    }

    protected function tearDownAfterTest()
    {
        $windowQty = $this->getAllWindowNames();
        if (count($windowQty) > 1 && end($windowQty) != 'null') {
            $this->selectWindow("name=" . end($windowQty));
            $this->close();
            $this->selectWindow(null);
        }
    }

    /**
     * @test
     * @author denis.poloka
     * @TestlinkId TL-MAGE-5999
     */
    public function withRequiredFieldsDefaultValue()
    {
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('admin_disable_push_to_robots');

        //Open XML Sitemap tab
        $loadData = $this->loadDataSet('XmlSitemap', 'admin_disable_push_to_robots');
        $this->systemConfigurationHelper()->openConfigurationTab('catalog_google_sitemap');

        //Verify
        $this->assertTrue($this->verifyForm($loadData['tab_1']['configuration']),
            'Enable Submission to Robots has not default value = No');
    }

    /**
     * @test
     * @author denis.poloka
     * @TestlinkId TL-MAGE-5841
     */
    public function withRequiredFieldsSave()
    {
        $this->navigate('system_configuration');

        //Enable push to robots.txt option
        $this->systemConfigurationHelper()->configure('admin_enable_push_to_robots');

        //Open XML Sitemap page
        $this->navigate('google_sitemap');

        //Create data
        $productData = $this->loadDataSet('XmlSitemap', 'new_xml_sitemap');

        //Click 'Add Sitemap' button
        $this->clickButton('add_sitemap', true);

        //Fill form and save sitemap
        $this->fillFieldset($productData, 'xml_sitemap_create');
        $this->clickButton('save_and_generate', true);
        $this->pleaseWait();
        $this->validatePage();

        //Check message
        $this->assertMessagePresent('error', 'success_saved_xml_sitemap');

        //Create sitemap link
        $uri = "sitemap.xml";
        $sitemapUrl = $this->xmlSitemapHelper()->getFileUrl($uri);

        //Create url in format [base url]/robots.txt an read the file
        $uri = "robots.txt";
        $robotsUrl = $this->xmlSitemapHelper()->getFileUrl($uri);
        $actualRobotsFile = $this->xmlSitemapHelper()->getFile($robotsUrl);

        //Find sitemap link in the robots.txt
        $this->assertContains($sitemapUrl, $actualRobotsFile, 'Stored Robots.txt don\'t have current sitemap!');
    }

    /**
     * @param string $emptyField
     * @test
     * @author denis.poloka
     * @dataProvider withRequiredFieldsEmptyDataProvider
     * @TestlinkId TL-MAGE-5841
     */
    public function withRequiredFieldsEmpty($emptyField)
    {
        //Create data
        $fieldData = $this->loadDataSet('XmlSitemap', 'new_xml_sitemap', array($emptyField => '%noValue%'));

        //Open XML Sitemap page
        $this->navigate('google_sitemap');

        //Click 'Add Sitemap' button
        $this->clickButton('add_sitemap', true);
        $this->waitForAjax();

        //Fill form and save sitemap
        $this->fillFieldset($fieldData, 'xml_sitemap_create');
        $this->clickButton('save', false);

        $xpath = $this->_getControlXpath('field', $emptyField);
        $this->addParameter('fieldXpath', $xpath);
        $this->assertMessagePresent('error', 'xml_sitemap_empty_required_field');
    }

    public function withRequiredFieldsEmptyDataProvider()
    {
        return array (
            array ('xml_sitemap_create_filename'),
            array ('xml_sitemap_create_path')
        );
    }

    /**
     * @test
     * @author denis.poloka
     * @TestlinkId TL-MAGE-5876
     */
    public function withRequiredFieldsPushRobots()
    {
        $this->navigate('system_configuration');

        //Enable push to robots.txt option
        $this->systemConfigurationHelper()->configure('admin_enable_push_to_robots');

        //Open Search Engine Robots tab
        $this->systemConfigurationHelper()->openConfigurationTab('general_design');

        //Fill "Edit custom instruction of robots.txt File" filed and save config
        $this->fillField('edit_custom_instruction', 'edit_custom_instruction_test');
        $this->clickButton('save_config');
        $this->assertMessagePresent('success', 'success_saved_config');

        //Create data
        $fieldData = $this->loadDataSet('XmlSitemap', 'admin_xml_sitemap');

        //Create url in format [base url]/robots.txt an read the file
        $uri = 'robots' . '.' . 'txt';
        $robotsUrl = $this->xmlSitemapHelper()->getFileUrl($uri);
        $order   = array("\r\n", "\n", "\r");
        $actualRobotsFile = str_replace($order, '', $this->xmlSitemapHelper()->getFile($robotsUrl));

        //get Robots.txt file and compare with expected content
        $expectedRobotsFile = $fieldData['tab_1']['configuration']['edit_custom_instruction_test'];
        $expectedRobotsTrim = str_replace($order, '', $expectedRobotsFile);

        //Compare file
        $this->assertContains($expectedRobotsTrim, $actualRobotsFile,
            'Robots.txt not contained custom instruction!');
    }

    /**
     * @test
     * @author denis.poloka
     * @TestlinkId TL-MAGE-5928
     */
    public function withRequiredFieldsSaveNotPush()
    {
        $websiteData = $this->loadDataSet('Website', 'generic_website');
        //Steps
        $this->navigate('manage_stores');
        $this->storeHelper()->createStore($websiteData, 'website');
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_website');
        $this->navigate('system_configuration');
        //Enable Submission to Robots.txt = "No" and save config
        $this->systemConfigurationHelper()->configure('admin_disable_push_to_robots');

        //Open Search Engine Robots tab
        $this->systemConfigurationHelper()->openConfigurationTab('general_design');

        //Fill "Edit custom instruction of robots.txt File" filed and save config
        $this->fillField('edit_custom_instruction', 'edit_custom_instruction_test');
        $this->clickButton('reset_to_default_robots', false);
        $this->waitForAjax();
        $this->clickButton('save_config');
        $this->assertMessagePresent('success', 'success_saved_config');

        //Create data
        $productData = $this->loadDataSet('XmlSitemap', 'new_xml_sitemap');

        //Open XML Sitemap page
        $this->navigate('google_sitemap');

        //Click 'Add Sitemap' button
        $this->clickButton('add_sitemap', true);
        $this->waitForAjax();

        //Fill form and save sitemap
        $this->fillFieldset($productData, 'xml_sitemap_create');
        $this->clickButton('save_and_generate', true);
        $this->pleaseWait();
        $this->validatePage();

        //Check message
        $this->assertMessagePresent('error', 'success_saved_xml_sitemap');

        //Create sitemap link
        $uri = "sitemap.xml";
        $sitemapUrl = $this->xmlSitemapHelper()->getFileUrl($uri);

        //Create url in format [base url]/robots.txt an read the file
        $uri = "robots.txt";
        $robotsUrl = $this->xmlSitemapHelper()->getFileUrl($uri);
        $actualRobotsFile = $this->xmlSitemapHelper()->getFile($robotsUrl);

        //Find sitemap link in the robots.txt
        $this->assertNotContains($sitemapUrl, $actualRobotsFile, 'Stored Robots.txt have current sitemap!');
    }

    /**
     * @test
     * @author denis.poloka
     * @TestlinkId TL-MAGE-5932
     */
    public function withRequiredFieldsEmptyReset()
    {
        //Enable push to robots.txt option
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('admin_enable_push_to_robots');

        //Open Search Engine Robots tab
        $this->systemConfigurationHelper()->openConfigurationTab('general_design');

        //Fill "Edit custom instruction of robots.txt File" filed and save config
        $this->fillField('edit_custom_instruction', 'edit_custom_instruction_test');
        $this->clickButton('reset_to_default_robots', false);
        $this->waitForAjax();
        $this->clickButton('save_config');
        $this->assertMessagePresent('success', 'success_saved_config');

        //Create url in format [base url]/robots.txt an read the file
        $uri = 'robots' . '.' . 'txt';
        $robotsUrl = $this->xmlSitemapHelper()->getFileUrl($uri);
        $order   = array("\r\n", "\n", "\r");
        $actualRobotsFile = str_replace($order, '', $this->xmlSitemapHelper()->getFile($robotsUrl));

        //get Robots.txt file and compare with expected content
        $expectedRobots = "User-agent: *"."Disallow: /index.php/"."Disallow: /*?"."Disallow: /*.js$".
            "Disallow: /*.css$"."Disallow: /checkout/"."Disallow: /app/".
            "Disallow: /downloader/"."Disallow: /js/"."Disallow: /lib/"."Disallow: /*.php$"."Disallow: /pkginfo/".
            "Disallow: /report/"."Disallow: /var/"."Disallow: /catalog/".
            "Disallow: /customer/"."Disallow: /sendfriend/"."Disallow: /review/"."Disallow: /*SID=";
        $expectedRobotsTrim = str_replace($order, '', $expectedRobots);

        //Compare file
        $this->assertEquals($expectedRobotsTrim, $actualRobotsFile,
            'Stored Robots.txt not equals to default instructions');
    }
}
