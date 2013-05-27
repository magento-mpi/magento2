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
        //Data
        $config = $this->loadDataSet('CustomerSegment', 'enable_customer_segment');
        //Steps
        $this->systemConfigurationHelper()->configure($config);
        $this->navigate($this->pageAfterAdminLogin);
        //Verification
        $this->assertEquals(true, $this->controlIsPresent('pageelement', 'menu_element_customer_segments'),
            'segment is disabled');
    }

    /**
     * @test
     * @TestlinkId TL-MAGE-1826
     */
    public function disableCustomerSegments()
    {
        //Data
        $config = $this->loadDataSet('CustomerSegment', 'disable_customer_segment');
        //Steps
        $this->systemConfigurationHelper()->configure($config);
        $this->navigate($this->pageAfterAdminLogin);
        //Verification
        $this->assertEquals(false, $this->controlIsPresent('pageelement', 'menu_element_customer_segments'),
            'segment is enabled');
    }

    /**
     * @test
     * @TestlinkId TL-MAGE-1855
     */
    public function enableCustomerSegmentsForCmsBanners()
    {
        //Data
        $config = $this->loadDataSet('CustomerSegment', 'enable_customer_segment');
        //Steps
        $this->systemConfigurationHelper()->configure($config);
        $this->navigate('manage_cms_banners');
        $this->clickButton('add_new_banner');
        //Verification
        $this->assertEquals(true, $this->controlIsPresent('dropdown', 'customer_segments'), 'segment is disabled');
    }

    /**
     * @test
     * @TestlinkId TL-MAGE-1856
     */
    public function disableCustomerSegmentsForCmsBanners()
    {
        //Data
        $config = $this->loadDataSet('CustomerSegment', 'disable_customer_segment');
        //Steps
        $this->systemConfigurationHelper()->configure($config);
        $this->navigate('manage_cms_banners');
        $this->clickButton('add_new_banner');
        //Verification
        $this->assertEquals(false, $this->controlIsPresent('dropdown', 'customer_segments'), 'segment is enabled');
    }

    /**
     * @test
     * @TestlinkId TL-MAGE-3763
     */
    public function enableCustomerSegmentsForProductRelations()
    {
        //Data
        $config = $this->loadDataSet('CustomerSegment', 'enable_customer_segment');
        //Steps
        $this->systemConfigurationHelper()->configure($config);
        $this->navigate('manage_product_rules');
        $this->clickButton('add_rule');
        //Verification
        $this->assertEquals(true, $this->controlIsPresent('dropdown', 'customer_segments'), 'segment is disabled');
    }

    /**
     * @test
     * @TestlinkId TL-MAGE-3763
     */
    public function disableCustomerSegmentsForProductRelations()
    {
        //Data
        $config = $this->loadDataSet('CustomerSegment', 'disable_customer_segment');
        //Steps
        $this->systemConfigurationHelper()->configure($config);
        $this->navigate('manage_product_rules');
        $this->clickButton('add_rule');
        //Verification
        $this->assertEquals(false, $this->controlIsPresent('dropdown', 'customer_segments'), 'segment is enabled');
    }

    /**
     * @test
     * @TestlinkId TL-MAGE-1860
     */
    public function enableCustomerSegmentsForSCPR()
    {
        //Data
        $config = $this->loadDataSet('CustomerSegment', 'enable_customer_segment');
        //Steps
        $this->systemConfigurationHelper()->configure($config);
        $this->navigate('manage_shopping_cart_price_rules');
        $this->clickButton('add_new_rule');
        $this->openTab('rule_conditions');
        //Verification
        $this->assertEquals(true, $this->controlIsPresent('pageelement', 'customer_segments'), 'segment is disabled');
    }

    /**
     * @test
     * @TestlinkId TL-MAGE-1860:
     */
    public function disableCustomerSegmentsForSCPR()
    {
        //Data
        $config = $this->loadDataSet('CustomerSegment', 'disable_customer_segment');
        //Steps
        $this->systemConfigurationHelper()->configure($config);
        $this->navigate('manage_shopping_cart_price_rules');
        $this->clickButton('add_new_rule');
        $this->openTab('rule_conditions');
        //Verification
        $this->assertEquals(false, $this->controlIsPresent('pageelement', 'customer_segments'), 'segment is enabled');
    }
}