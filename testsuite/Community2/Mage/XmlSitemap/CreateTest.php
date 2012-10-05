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
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Xml Sitemap Admin Page
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Community2_Mage_XmlSitemap_CreateTest extends Mage_Selenium_TestCase
{
    /**
     * <p>Preconditions:</p>
     * <p>1. Login to Admin page</p>
     * <p>2. Disable Http only</p>
     * <p>3. Disable Secret key</p>
     */
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('admin_disable_http_only');
        $this->systemConfigurationHelper()->configure('admin_disable_secret_key');
        $this->systemConfigurationHelper()->configure('admin_disable_push_to_robots');
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
     * <p>Verifying default value of option "Enable Submission to Robots.txt"</p>
     * <p>Steps</p>
     * <p>1. Open Search Engine Submission Settings</p>
     * <p>2. Option 'Enable Submission to Robots.txt'</p>
     * <p>Expected result:</p>
     * <p>"Enable Submission to Robots.txt' = "No" (default)</p>
     *
     * @test
     * @author denis.poloka
     * @TestlinkId TL-MAGE-5999
     */
    public function withRequiredFieldsDefaultValue ()
    {
        //Open XML Sitemap tab
        $loadData = $this->loadDataSet('XmlSitemap', 'admin_disable_push_to_robots');
        $this->systemConfigurationHelper()->openConfigurationTab('catalog_google_sitemap');

        //Verify
        $this->assertTrue($this->verifyForm($loadData['tab_1']['configuration']),
            'Enable Submission to Robots has not default value = No');
    }

    /**
     * <p>Verifying Save process of XML Sitemap</p>
     * <p>Steps</p>
     * <p>1. Go to XML Sitemap</p>
     * <p>2. Set filename and path</p>
     * <p>3. Click "Save and Generate" button</p>
     * <p>4. Sitemap has saved</p>
     * <p>Expected result:</p>
     * <p>Sitemap created, pushed to robots.txt and success message was appear</p>
     * <p>Message "The sitemap has been saved" is displayed</p>
     *
     * @test
     * @author denis.poloka
     * @TestlinkId TL-MAGE-5841
     */
    public function withRequiredFieldsSave ()
    {
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
     * <p>Verifying Required field of XML Sitemap</p>
     * <p>Steps</p>
     * <p>1. Go to XML Sitemap</p>
     * <p>2. Set filename and path with noValue </p>
     * <p>3.Click Save button</p>
     * <p>Expected result:</p>
     * <p>XML Sitemap doesn't created</p>
     * <p>Message "This is a required field." is displayed</p>
     *
     * @param string $emptyField
     * @param string $messageCount
     *
     * @test
     * @author denis.poloka
     * @dataProvider withRequiredFieldsEmptyDataProvider
     * @TestlinkId TL-MAGE-5841
     */
    public function withRequiredFieldsEmpty ($emptyField, $messageCount)
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
        $this->assertTrue($this->verifyMessagesCount($messageCount), $this->getParsedMessages());
    }

    public function withRequiredFieldsEmptyDataProvider()
    {
        return array (
            array ('xml_sitemap_create_filename', 1),
            array ('xml_sitemap_create_path', 1)
        );
    }

    /**
     * <p>Verifying "Edit custom instruction of robots.txt File" push to default Robots.txt</p>
     * <p>Steps</p>
     * <p>1. Open Search Engine Robots tab</p>
     * <p>2. Fill "Edit custom instruction of robots.txt File" filed and save config </p>
     * <p>3. Save config</p>
     * <p>4. Open file robots.txt
     * <p>Expected result:</p>
     * <p>Robots.txt should contained information from field "Edit custom instruction of robots.txt File"</p>
     *
     * @test
     * @author denis.poloka
     * @TestlinkId TL-MAGE-5876
     */
    public function withRequiredFieldsPushRobots ()
    {
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
        $uri = 'robots'.'.'.'txt';
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
     * <p>Verifying Save process of XML Sitemap with Enable Submission to Robots.txt = "No"</p>
     * <p>Steps</p>
     * <p>1. Enable Submission to Robots.txt = "No"</p>
     * <p>1. Go to XML Sitemap</p>
     * <p>2. Set filename and path</p>
     * <p>3. Click Save and Generate button</p>
     * <p>4. Sitemap has saved and generated</p>
     * <p>Expected result:</p>
     * <p>Sitemap created and success message was appear and do not pushed to robots.txt</p>
     * <p>Message "The sitemap has been saved" is displayed</p>
     *
     * @test
     * @author denis.poloka
     * @TestlinkId TL-MAGE-5928
     */
    public function withRequiredFieldsSaveNotPush ()
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
     * <p>Verifying Reset to Default button</p>
     * <p>Steps</p>
     * <p>1. Open Search Engine Robots tab</p>
     * <p>2. Fill "Edit custom instruction of robots.txt File" filed </p>
     * <p>3. Push Reset to Default button</p>
     * <p>3. Save config</p>
     * <p>4. Open file robots.txt
     * <p>Expected result:</p>
     * <p>Robots.txt should contained predefined fields"</p>
     *
     * @test
     * @author denis.poloka
     * @TestlinkId TL-MAGE-5932
     */
    public function withRequiredFieldsEmptyReset ()
    {
        //Enable push to robots.txt option
        //$this->systemConfigurationHelper()->configure('admin_enable_push_to_robots');

        //Open Search Engine Robots tab
        $this->systemConfigurationHelper()->openConfigurationTab('general_design');

        //Fill "Edit custom instruction of robots.txt File" filed and save config
        $this->fillField('edit_custom_instruction', 'edit_custom_instruction_test');
        $this->clickButton('reset_to_default_robots', false);
        $this->waitForAjax();
        $this->clickButton('save_config');
        $this->assertMessagePresent('success', 'success_saved_config');

        //Create url in format [base url]/robots.txt an read the file
        $uri = 'robots'.'.'.'txt';
        $robotsUrl = $this->xmlSitemapHelper()->getFileUrl($uri);
        $order   = array("\r\n", "\n", "\r");
        $actualRobotsFile = str_replace($order, '', $this->xmlSitemapHelper()->getFile($robotsUrl));

        //get Robots.txt file and compare with expected content
        $expectedRobots = "User-agent: *"."Disallow: /index.php/"."Disallow: /*?"."Disallow: /*.js$".
            "Disallow: /*.css$"."Disallow: /checkout/"."Disallow: /tag/"."Disallow: /app/".
            "Disallow: /downloader/"."Disallow: /js/"."Disallow: /lib/"."Disallow: /*.php$"."Disallow: /pkginfo/".
            "Disallow: /report/"."Disallow: /skin/"."Disallow: /var/"."Disallow: /catalog/".
            "Disallow: /customer/"."Disallow: /sendfriend/"."Disallow: /review/"."Disallow: /*SID=";
        $expectedRobotsTrim = str_replace($order, '', $expectedRobots);

        //Compare file
        $this->assertEquals($expectedRobotsTrim, $actualRobotsFile,
            'Stored Robots.txt not equals to default instructions');
    }
}