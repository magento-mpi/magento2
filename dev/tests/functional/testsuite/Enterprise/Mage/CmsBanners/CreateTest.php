<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_CmsBanners
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Create Cms Banner Test
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Enterprise_Mage_CmsBanners_CreateTest extends Mage_Selenium_TestCase
{
    protected function assertPreconditions()
    {
        $this->loginAdminUser();
    }

    /**
     * <p>Preconditions</p>
     * <p>Creates Category, Product and Promotion Rules to use during tests</p>
     *
     * @return array
     * @test
     */
    public function preconditionsForTests()
    {
        //Data
        $category = $this->loadDataSet('Category', 'sub_category_required');
        $priceRuleData = $this->loadDataSet('CatalogPriceRule', 'test_catalog_rule');
        $ruleData = $this->loadDataSet('ShoppingCartPriceRule', 'scpr_required_fields');
        $product = $this->loadDataSet('Product', 'simple_product_visible',
            array('general_categories' => $category['parent_category'] . '/' . $category['name']));
        //Steps Crating Categories
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('SingleStoreMode/disable_single_store_mode');
        $this->navigate('manage_categories', false);
        $this->categoryHelper()->checkCategoriesPage();
        $this->categoryHelper()->createCategory($category);
        //VerifyingCategories
        $this->assertMessagePresent('success', 'success_saved_category');
        //Steps Crating Products
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($product);
        //Verifying Products
        $this->assertMessagePresent('success', 'success_saved_product');
        //Steps Crating Catalog Price Rules
        $this->navigate('manage_catalog_price_rules');
        $this->priceRulesHelper()->createRule($priceRuleData);
        //Verification Catalog Price Rules
        $this->assertMessagePresent('success', 'success_saved_rule');
        //Steps Creating  Shopping Cart Price Rule
        $this->navigate('manage_shopping_cart_price_rules');
        $this->priceRulesHelper()->createRule($ruleData);
        //Verification Shopping Cart Price Rule
        $this->assertMessagePresent('success', 'success_saved_rule');

        return array(
            'category_path' => $product['general_categories'],
            'filter_sku' => $product['general_sku'],
            'catalog_rule_name' => $priceRuleData['info']['rule_name'],
            'price_rule_name' => $ruleData['info']['rule_name']
        );
    }

    /**
     * <p>Creates Banner with required fields</p>
     *
     * @test
     * @TestlinkId TL-MAGE-6024
     */
    public function withRequiredFields()
    {
        //Data
        $pageData = $this->loadDataSet('CmsBanners', 'new_cms_banner_req');
        //Steps
        $this->navigate('manage_cms_banners');
        $this->cmsBannersHelper()->createCmsBanner($pageData);
        //Verification
        $this->assertMessagePresent('success', 'success_saved_cms_banner');
    }

    /**
     * <p>Creates Banner with all fields filled except one empty</p>
     *
     * @param string $fieldName
     * @param string $errorMessage
     *
     * @test
     * @dataProvider withEmptyRequiredFieldsDataProvider
     * @depends withRequiredFields
     * @TestlinkId TL-MAGE-6025
     */
    public function withEmptyRequiredFields($fieldName, $errorMessage)
    {
        //Data
        $pageData = $this->loadDataSet('CmsBanners', 'new_cms_banner_req', array($fieldName => '%noValue%'));
        //Steps
        $this->navigate('manage_cms_banners');
        $this->cmsBannersHelper()->createCmsBanner($pageData);
        //Verification
        if ($fieldName === 'content_area') {
            $this->assertMessagePresent('error', $errorMessage);
        } else {
            $this->addFieldIdToMessage('field', $fieldName);
            $this->assertMessagePresent('validation', $errorMessage);
            $this->assertTrue($this->verifyMessagesCount(), $this->getParsedMessages());
        }
    }

    public function withEmptyRequiredFieldsDataProvider()
    {
        return array(
            array('banner_properties_name', 'empty_required_field'),
            array('content_area', 'empty_content_field')
        );
    }

    /**
     * <p>Creates non-active banner with specified Banners type</p>
     *
     * @return array
     *
     * @test
     * @depends withRequiredFields
     * @TestlinkId TL-MAGE-6026
     */
    public function withSpecialSettings()
    {
        //Data
        $pageData = $this->loadDataSet('CmsBanners', 'new_cms_banner_req', array(
            'active' => 'No',
            'applies_to' => 'Specified Banner Types',
            'specify_types' => 'Content Area, Header, Right Column'
        ));
        //Steps
        $this->navigate('manage_cms_banners');
        $this->cmsBannersHelper()->createCmsBanner($pageData);
        //Verification
        $this->assertMessagePresent('success', 'success_saved_cms_banner');
    }

    /**
     * <p>Create CMS Banner with long values in required fields</p>
     *
     * @test
     * @depends withRequiredFields
     * @TestlinkId TL-MAGE-6027
     */
    public function withLongValues()
    {
        //Data
        $pageData = $this->loadDataSet('CmsBanners', 'new_cms_banner_req',
            array('banner_properties_name' => $this->generate('string', 255, ':alnum:')));
        //Steps
        $this->navigate('manage_cms_banners');
        $this->cmsBannersHelper()->createCmsBanner($pageData);
        //Verification
        $this->assertMessagePresent('success', 'success_saved_cms_banner');
    }

    /**
     * <p>Create CMS Banner with empty Store View Specific Content</p>
     *
     * @test
     * @depends withRequiredFields
     * @TestlinkId TL-MAGE-6028
     */
    public function withEmptySpecificContent()
    {
        //Data
        $pageData = $this->loadDataSet('CmsBanners', 'new_cms_banner_req', array(
            'no_default_content' => 'Yes',
            'specific_content_use_default' => 'No',
            'content_area' => '%noValue%'
        ));
        //Steps
        $this->navigate('manage_cms_banners');
        $this->cmsBannersHelper()->createCmsBanner($pageData);
        //Verification
        $this->assertMessagePresent('error', 'empty_content_field');

    }

    /**
     * <p>Create CMS Banner with all widgets type</p>
     *
     * @param array $widgetData
     *
     * @test
     * @depends preconditionsForTests
     * @depends withRequiredFields
     * @TestlinkId TL-MAGE-6029
     */
    public function withAllWidgetsType($widgetData)
    {
        //Data
        $pageData = $this->loadDataSet('CmsBanners', 'new_cms_banner_all_fields',
            array('filter_sku' => $widgetData['filter_sku']));
        //Steps
        $this->navigate('manage_cms_banners');
        $this->cmsBannersHelper()->createCmsBanner($pageData);
        //Verification
        $this->assertMessagePresent('success', 'success_saved_cms_banner');

    }

    /**
     * <p>Create CMS Banner with all widgets type for Specific Content</p>
     *
     * @param array $widgetData
     *
     * @test
     * @depends preconditionsForTests
     * @depends withRequiredFields
     * @TestlinkId TL-MAGE-6030
     */
    public function withAllWidgetsTypeSpecificContent($widgetData)
    {
        //Data
        $pageData = $this->loadDataSet('CmsBanners', 'new_cms_banner_all_fields', array(
            'no_default_content' => 'Yes',
            'content_area' => '%noValue%',
            'specific_content_use_default' => 'No',
            'filter_sku' => $widgetData['filter_sku']
        ));
        //Steps
        $this->navigate('manage_cms_banners');
        $this->cmsBannersHelper()->createCmsBanner($pageData);
        //Verification
        $this->assertMessagePresent('success', 'success_saved_cms_banner');
    }

    /**
     * <p>Create CMS Banner with all variables</p>
     *
     * @test
     * @depends withRequiredFields
     */
    public function withAllVariables()
    {
        //Data
        $pageData = $this->loadDataSet('CmsBanners', 'new_cms_banner_with_variable');
        //Steps
        $this->navigate('manage_cms_banners');
        $this->cmsBannersHelper()->createCmsBanner($pageData);
        //Verification
        $this->assertMessagePresent('success', 'success_saved_cms_banner');

    }

    /**
     * <p>Create CMS Banner with all variables for block Store View Specific Content</p>
     *
     * @test
     * @depends withRequiredFields
     * @TestlinkId TL-MAGE-6031
     */
    public function withAllVariablesSpecificContent()
    {
        //Data
        $pageData = $this->loadDataSet('CmsBanners', 'new_cms_banner_with_variable',
            array('no_default_content' => 'Yes', 'specific_content_use_default' => 'No'));
        //Steps
        $this->navigate('manage_cms_banners');
        $this->cmsBannersHelper()->createCmsBanner($pageData);
        //Verification
        $this->assertMessagePresent('success', 'success_saved_cms_banner');

    }

    /**
     * <p>Create CMS Banner with Related Promotions Rules</p>
     *
     * @param array $priceRuleData
     *
     * @test
     * @depends preconditionsForTests
     * @depends withRequiredFields
     * @TestlinkId TL-MAGE-6032
     */
    public function withRelatedPromotionsRules($priceRuleData)
    {
        //Data
        $pageData = $this->loadDataSet('CmsBanners', 'new_cms_banner_with_rules');
        $pageData['related_promotions']['catalog_rule'] = $priceRuleData['catalog_rule_name'];
        $pageData['related_promotions']['price_rule'] = $priceRuleData['price_rule_name'];
        //Steps
        $this->navigate('manage_cms_banners');
        $this->cmsBannersHelper()->createCmsBanner($pageData);
        //Verification
        $this->assertMessagePresent('success', 'success_saved_cms_banner');
    }
}
