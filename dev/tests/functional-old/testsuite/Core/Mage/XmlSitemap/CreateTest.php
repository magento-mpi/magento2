<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Xml Sitemap Admin Page
 *
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_XmlSitemap_CreateTest extends Mage_Selenium_TestCase
{
    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('General/disable_http_only');
        $this->systemConfigurationHelper()->configure('Advanced/disable_secret_key');
        $this->systemConfigurationHelper()->configure('XmlSitemap/admin_disable_push_to_robots');
        $this->systemConfigurationHelper()->configure('SingleStoreMode/disable_single_store_mode');
    }

    protected function assertPreConditions()
    {
        $this->loginAdminUser();
    }

    public function tearDownAfterTestClass()
    {
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('General/enable_http_only');
        $this->systemConfigurationHelper()->configure('Advanced/enable_secret_key');
        $this->systemConfigurationHelper()->configure('XmlSitemap/admin_disable_push_to_robots');
        $this->systemConfigurationHelper()->configure('SingleStoreMode/enable_single_store_mode');
        $this->systemConfigurationHelper()->openConfigurationTab('general_design');
        $this->systemConfigurationHelper()->expandFieldSet('search_engine_robots');
        $this->fillField('edit_custom_instruction', '');
        $this->clickButton('save_config');
    }

    /**
     * <p>Verifying default value of option "Enable Submission to Robots.txt"</p>
     *
     * @test
     * @TestlinkId TL-MAGE-5999
     */
    public function withRequiredFieldsDefaultValue()
    {
        $loadData = $this->loadDataSet('XmlSitemap', 'admin_disable_push_to_robots');
        $tab = $loadData['tab_1']['tab_name'];
        //Open XML Sitemap tab
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->openConfigurationTab($tab);
        //Verify
        $this->systemConfigurationHelper()->verifyConfigurationOptions($loadData['tab_1']['configuration'], $tab);
    }

    /**
     * <p>Verifying Save process of XML Sitemap</p>
     *
     * @test
     * @TestlinkId TL-MAGE-5841
     */
    public function withRequiredFieldsSave()
    {
        $this->markTestIncomplete('MAGETWO-9802');
        //Create data
        $xmlSitemap = $this->loadDataSet('XmlSitemap', 'new_xml_sitemap');
        //Enable push to robots.txt option
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('XmlSitemap/admin_enable_push_to_robots');
        //Open XML Sitemap page
        $this->navigate('xml_sitemap');
        //Click 'Add Sitemap' button
        $this->clickButton('add_sitemap');
        //Fill form and save sitemap
        $this->fillFieldset($xmlSitemap, 'xml_sitemap_create');
        $this->saveForm('save_and_generate');
        //Check message
        $this->assertMessagePresent('success', 'success_saved_xml_sitemap');
        $sitemapUrl = $this->xmlSitemapHelper()->getFileUrl('sitemap.xml');
        //Create url in format [base url]/robots.txt an read the file
        $robotsUrl = $this->xmlSitemapHelper()->getFileUrl('robots.txt');
        $actualRobotsFile = $this->getFile($robotsUrl);
        //Find sitemap link in the robots.txt
        $this->assertContains($sitemapUrl, $actualRobotsFile, 'Stored Robots.txt don\'t have current sitemap!');
    }

    /**
     * <p>Verifying Required field of XML Sitemap</p>
     *
     * @param string $emptyField
     *
     * @test
     * @dataProvider withRequiredFieldsEmptyDataProvider
     * @TestlinkId TL-MAGE-5841
     */
    public function withRequiredFieldsEmpty($emptyField)
    {
        //Create data
        $fieldData = $this->loadDataSet('XmlSitemap', 'new_xml_sitemap', array($emptyField => '%noValue%'));
        //Open XML Sitemap page
        $this->navigate('xml_sitemap');
        //Click 'Add Sitemap' button
        $this->clickButton('add_sitemap');
        //Fill form and save sitemap
        $this->fillFieldset($fieldData, 'xml_sitemap_create');
        $this->saveForm('save');
        $this->addFieldIdToMessage('field', $emptyField);
        $this->assertMessagePresent('validation', 'empty_required_field');
        $this->assertTrue($this->verifyMessagesCount(), $this->getParsedMessages());
    }

    public function withRequiredFieldsEmptyDataProvider()
    {
        return array(
            array('xml_sitemap_create_filename'),
            array('xml_sitemap_create_path')
        );
    }

    /**
     * <p>Verifying "Edit custom instruction of robots.txt File" push to default Robots.txt</p>
     *
     * @test
     * @TestlinkId TL-MAGE-5876
     */
    public function withRequiredFieldsPushRobots()
    {
        $fieldData = $this->loadDataSet('XmlSitemap', 'admin_xml_sitemap/tab_1/configuration/search_engine_robots');
        //Enable push to robots.txt option
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('XmlSitemap/admin_enable_push_to_robots');
        //Open Search Engine Robots tab
        $this->systemConfigurationHelper()->openConfigurationTab('general_design');
        $this->systemConfigurationHelper()->expandFieldSet('search_engine_robots');
        //Fill "Edit custom instruction of robots.txt File" filed and save config
        $this->fillField('edit_custom_instruction', $fieldData['edit_custom_instruction']);
        $this->clickButton('save_config');
        $this->assertMessagePresent('success', 'success_saved_config');
        //Create url in format [base url]/robots.txt an read the file
        $robotsUrl = $this->xmlSitemapHelper()->getFileUrl('robots.txt');
        $order = array("\r\n", "\n", "\r");
        $actualRobotsFile = str_replace($order, '', $this->getFile($robotsUrl));
        //get Robots.txt file and compare with expected content
        $expectedRobotsTrim = str_replace($order, '', $fieldData['edit_custom_instruction']);
        //Compare file
        $this->assertContains($expectedRobotsTrim, $actualRobotsFile, 'Robots.txt not contained custom instruction!');
    }

    /**
     * <p>Verifying Save process of XML Sitemap with Enable Submission to Robots.txt = "No"</p>
     *
     * @test
     * @TestlinkId TL-MAGE-5928
     */
    public function withRequiredFieldsSaveNotPush()
    {
        $productData = $this->loadDataSet('XmlSitemap', 'new_xml_sitemap');
        //Steps
        $this->navigate('system_configuration');
        //Enable Submission to Robots.txt = "No" and save config
        $this->systemConfigurationHelper()->configure('XmlSitemap/admin_disable_push_to_robots');
        //Open Search Engine Robots tab
        $this->systemConfigurationHelper()->openConfigurationTab('general_design');
        $this->systemConfigurationHelper()->expandFieldSet('search_engine_robots');
        //Fill "Edit custom instruction of robots.txt File" filed and save config
        $this->fillField('edit_custom_instruction', 'edit_custom_instruction_test');
        $this->clickButton('reset_to_default_robots', false);
        $this->waitForAjax();
        $this->clickButton('save_config');
        $this->assertMessagePresent('success', 'success_saved_config');
        //Open XML Sitemap page
        $this->navigate('xml_sitemap');
        //Click 'Add Sitemap' button
        $this->clickButton('add_sitemap');
        //Fill form and save sitemap
        $this->fillFieldset($productData, 'xml_sitemap_create');
        $this->saveForm('save_and_generate');
        //Check message
        $this->assertMessagePresent('success', 'success_saved_xml_sitemap');
        //Create sitemap link
        $sitemapUrl = $this->xmlSitemapHelper()->getFileUrl('sitemap.xml');
        //Create url in format [base url]/robots.txt an read the file
        $robotsUrl = $this->xmlSitemapHelper()->getFileUrl('robots.txt');
        $actualRobotsFile = $this->getFile($robotsUrl);
        //Find sitemap link in the robots.txt
        $this->assertNotContains($sitemapUrl, $actualRobotsFile, 'Stored Robots.txt have current sitemap!');
    }

    /**
     * <p>Verifying Reset to Default button</p>
     *
     * @test
     * @TestlinkId TL-MAGE-5932
     */
    public function withRequiredFieldsEmptyReset()
    {
        //Enable push to robots.txt option
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('XmlSitemap/admin_enable_push_to_robots');
        //Open Search Engine Robots tab
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->openConfigurationTab('general_design');
        $this->systemConfigurationHelper()->expandFieldSet('search_engine_robots');
        //Fill "Edit custom instruction of robots.txt File" filed and save config
        $this->fillField('edit_custom_instruction', 'edit_custom_instruction_test');
        $this->clickButton('reset_to_default_robots', false);
        $this->waitForAjax();
        $this->clickButton('save_config');
        $this->assertMessagePresent('success', 'success_saved_config');
        //Create url in format [base url]/robots.txt an read the file
        $robotsUrl = $this->xmlSitemapHelper()->getFileUrl('robots.txt');
        $order = array("\r\n", "\n", "\r");
        $actualRobotsFile = str_replace($order, '', $this->getFile($robotsUrl));
        //get Robots.txt file and compare with expected content
        $expectedRobots = 'User-agent: *Disallow: /index.php/Disallow: /*?Disallow: /*.js$Disallow: /*.css$'
            . 'Disallow: /checkout/Disallow: /app/Disallow: /js/Disallow: /lib/'
            . 'Disallow: /*.php$Disallow: /pkginfo/Disallow: /report/Disallow: /var/'
            . 'Disallow: /catalog/Disallow: /customer/Disallow: /sendfriend/Disallow: /review/Disallow: /*SID=';
        $expectedRobotsTrim = str_replace($order, '', $expectedRobots);
        //Compare file
        $this->assertEquals(
            $expectedRobotsTrim,
            $actualRobotsFile,
            'Stored Robots.txt not equals to default instructions'
        );
    }
}
