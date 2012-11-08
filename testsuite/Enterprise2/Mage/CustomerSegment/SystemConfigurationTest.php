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
class Enterprise2_Mage_CustomerSegment_SystemConfigurationTest extends Mage_Selenium_TestCase
{
    public function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->openConfigurationTab('customers_customer_configuration');
    }

    /**
     * @test
     * @TestlinkId TL-MAGE-1823
     */
    public function enableCustomerSegments()
    {
        //Steps
        $this->fillDropdown('enable_customer_segment_functionality', 'Yes');
        $this->clickButton('save_config');
        //Verification
        $this->assertMessagePresent('success', 'success_saved_config');
        //Steps
        $this->navigate('dashboard');
        $menuElementXpath = $this->_getControlXpath('pageelement', 'menu_element_customer_segments');
        //Verification
        if(!$this->isElementPresent($menuElementXpath)) {
            $this->fail('Menu element Customer Segments is absent');
        }
    }

    /**
     * @test
     * @TestlinkId TL-MAGE-1826
     */
    public function disableCustomerSegments()
    {
        //Steps
        $this->fillDropdown('enable_customer_segment_functionality', 'No');
        $this->clickButton('save_config');
        //Verification
        $this->assertMessagePresent('success', 'success_saved_config');
        //Steps
        $this->navigate('dashboard');
        $menuElementXpath = $this->_getControlXpath('pageelement', 'menu_element_customer_segments');
        //Verification
        if($this->isElementPresent($menuElementXpath)) {
            $this->fail('Menu element Customer Segments is present');
        }
    }

    /**
     * @test
     * @TestlinkId TL-MAGE-1855
     */
    public function enableCustomerSegmentsForCmsBanners()
    {
        //Steps
        $this->fillDropdown('enable_customer_segment_functionality', 'Yes');
        $this->clickButton('save_config');
        //Verification
        $this->assertMessagePresent('success', 'success_saved_config');
        //Steps
        $this->navigate('manage_cms_banners');
        $this->clickButton('add_new_banner');
        $menuElementXpath = $this->_getControlXpath('dropdown', 'customer_segments');
        //Verification
        if(!$this->isElementPresent($menuElementXpath)) {
            $this->fail('Customer Segments dropdown is absent on create new cms banner page');
        }
    }

    /**
     * @test
     * @TestlinkId TL-MAGE-1856
     */
    public function disableCustomerSegmentsForCmsBanners()
    {
        //Steps
        $this->fillDropdown('enable_customer_segment_functionality', 'No');
        $this->clickButton('save_config');
        //Verification
        $this->assertMessagePresent('success', 'success_saved_config');
        //Steps
        $this->navigate('manage_cms_banners');
        $this->clickButton('add_new_banner');
        $menuElementXpath = $this->_getControlXpath('dropdown', 'customer_segments');
        //Verification
        if($this->isElementPresent($menuElementXpath)) {
            $this->fail('Customer Segments dropdown is present on create new cms banner page');
        }
    }

    /**
     * @test
     * @TestlinkId TL-MAGE-3763
     */
    public function enableCustomerSegmentsForProductRelations()
    {
        //Steps
        $this->fillDropdown('enable_customer_segment_functionality', 'Yes');
        $this->clickButton('save_config');
        //Verification
        $this->assertMessagePresent('success', 'success_saved_config');
        //Steps
        $this->navigate('manage_product_rules');
        $this->clickButton('add_rule');
        $menuElementXpath = $this->_getControlXpath('dropdown', 'customer_segments');
        //Verification
        if(!$this->isElementPresent($menuElementXpath)) {
            $this->fail('Customer Segments dropdown is absent on create new product rule page');
        }
    }

    /**
     * @test
     * @TestlinkId TL-MAGE-3763
     */
    public function disableCustomerSegmentsForProductRelations()
    {
        //Steps
        $this->fillDropdown('enable_customer_segment_functionality', 'No');
        $this->clickButton('save_config');
        //Verification
        $this->assertMessagePresent('success', 'success_saved_config');
        //Steps
        $this->navigate('manage_product_rules');
        $this->clickButton('add_rule');
        $menuElementXpath = $this->_getControlXpath('dropdown', 'customer_segments');
        //Verification
        if($this->isElementPresent($menuElementXpath)) {
            $this->fail('Customer Segments dropdown is present create new product rule page');
        }
    }

    /**
     * @test
     * @TestlinkId TL-MAGE-1860
     */
    public function enableCustomerSegmentsForSCPR()
    {
        //Steps
        $this->fillDropdown('enable_customer_segment_functionality', 'Yes');
        $this->clickButton('save_config');
        //Verification
        $this->assertMessagePresent('success', 'success_saved_config');
        //Steps
        $this->navigate('manage_shopping_cart_price_rules');
        $this->clickButton('add_new_rule');
        $this->openTab('rule_conditions');
        $menuElementXpath = $this->_getControlXpath('pageelement', 'customer_segments');
        //Verification
        if(!$this->isElementPresent($menuElementXpath)) {
            $this->fail('Customer Segments option is absent in conditions dropdown on new shopping cart price rules
            page');
        }
    }

    /**
     * @test
     * @TestlinkId TL-MAGE-1860:
     */
    public function disableCustomerSegmentsForSCPR()
    {
        //Steps
        $this->fillDropdown('enable_customer_segment_functionality', 'No');
        $this->clickButton('save_config');
        //Verification
        $this->assertMessagePresent('success', 'success_saved_config');
        //Steps
        $this->navigate('manage_shopping_cart_price_rules');
        $this->clickButton('add_new_rule');
        $this->openTab('rule_conditions');
        $menuElementXpath = $this->_getControlXpath('pageelement', 'customer_segments');
        //Verification
        if($this->isElementPresent($menuElementXpath)) {
            $this->fail('Customer Segments option is present in conditions dropdown on new shopping cart price rules
            page');
        }
    }
}