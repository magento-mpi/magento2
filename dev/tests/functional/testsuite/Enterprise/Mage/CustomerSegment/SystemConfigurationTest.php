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
 * Enabling and disabling Customer Segments Functionality
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Enterprise_Mage_CustomerSegment_SystemConfigurationTest extends Mage_Selenium_TestCase
{
    public function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->navigate('system_configuration');
    }

    /**
     * @test
     * @TestlinkId TL-MAGE-1823
     */
    public function enableCustomerSegments()
    {
        //Steps
        $this->systemConfigurationHelper()->configure('CustomerSegment/enable_customer_segment');
        $this->navigate($this->pageAfterAdminLogin);
        //Verification
        $locator = $this->getUimapPage('admin', 'manage_customer_segments')->getClickXpath();
        $this->assertTrue((bool)$this->elementIsPresent($locator), 'segment is disabled');
    }

    /**
     * @test
     * @TestlinkId TL-MAGE-1826
     */
    public function disableCustomerSegments()
    {
        //Steps
        $this->systemConfigurationHelper()->configure('CustomerSegment/disable_customer_segment');
        $this->navigate($this->pageAfterAdminLogin);
        //Verification
        $locator = $this->getUimapPage('admin', 'manage_customer_segments')->getClickXpath();
        $this->assertFalse((bool)$this->elementIsPresent($locator), 'segment is disabled');
    }

    /**
     * @test
     * @TestlinkId TL-MAGE-1855
     */
    public function enableCustomerSegmentsForCmsBanners()
    {
        //Steps
        $this->systemConfigurationHelper()->configure('CustomerSegment/enable_customer_segment');
        $this->navigate('manage_cms_banners');
        $this->clickButton('add_new_banner');
        //Verification
        $this->assertTrue($this->controlIsPresent('dropdown', 'customer_segments'), 'segment is disabled');
    }

    /**
     * @test
     * @TestlinkId TL-MAGE-1856
     */
    public function disableCustomerSegmentsForCmsBanners()
    {
        //Steps
        $this->systemConfigurationHelper()->configure('CustomerSegment/disable_customer_segment');
        $this->navigate('manage_cms_banners');
        $this->clickButton('add_new_banner');
        //Verification
        $this->assertFalse($this->controlIsPresent('dropdown', 'customer_segments'), 'segment is enabled');
    }

    /**
     * @test
     * @TestlinkId TL-MAGE-3763
     */
    public function enableCustomerSegmentsForProductRelations()
    {
        //Steps
        $this->systemConfigurationHelper()->configure('CustomerSegment/enable_customer_segment');
        $this->navigate('manage_product_rules');
        $this->clickButton('add_rule');
        //Verification
        $this->assertTrue($this->controlIsPresent('dropdown', 'customer_segments'), 'segment is disabled');
    }

    /**
     * @test
     * @TestlinkId TL-MAGE-3763
     */
    public function disableCustomerSegmentsForProductRelations()
    {
        //Steps
        $this->systemConfigurationHelper()->configure('CustomerSegment/disable_customer_segment');
        $this->navigate('manage_product_rules');
        $this->clickButton('add_rule');
        //Verification
        $this->assertFalse($this->controlIsPresent('dropdown', 'customer_segments'), 'segment is enabled');
    }

    /**
     * @test
     * @TestlinkId TL-MAGE-1860
     */
    public function enableCustomerSegmentsForSCPR()
    {
        //Steps
        $this->systemConfigurationHelper()->configure('CustomerSegment/enable_customer_segment');
        $this->navigate('manage_shopping_cart_price_rules');
        $this->clickButton('add_new_rule');
        $this->openTab('rule_conditions');
        //Verification
        $this->assertTrue($this->controlIsPresent('pageelement', 'customer_segments'), 'segment is disabled');
    }

    /**
     * @test
     * @TestlinkId TL-MAGE-1860:
     */
    public function disableCustomerSegmentsForSCPR()
    {
        //Steps
        $this->systemConfigurationHelper()->configure('CustomerSegment/disable_customer_segment');
        $this->navigate('manage_shopping_cart_price_rules');
        $this->clickButton('add_new_rule');
        $this->openTab('rule_conditions');
        //Verification
        $this->assertFalse($this->controlIsPresent('pageelement', 'customer_segments'), 'segment is enabled');
    }
}