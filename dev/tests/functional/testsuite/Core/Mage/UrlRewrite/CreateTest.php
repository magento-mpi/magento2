<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @copyright   {copyright}
 * @license     {license_link}
 *
 */

/**
 * Url Rewrite Admin Page
 */
class Core_Mage_UrlRewrite_CreateTest extends Mage_Selenium_TestCase
{
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
    }

    protected function tearDownAfterTestClass()
    {
        $this->frontend();
        $this->addParameter('store', 'Main Website Store');
        $this->clickControl(self::FIELD_TYPE_LINK, 'select_store', false);
        $this->validatePage();
    }

    /**
     * Verify that url rewrite form is present at the backend page
     *
     * @test
     */
    public function isFormPresent()
    {
        //Steps
        $this->navigate('url_rewrite_management');
        $this->clickButton('add_new_rewrite');
        //Verifying
        $this->assertTrue($this->controlIsPresent(self::FIELD_TYPE_DROPDOWN, 'rewrite_type'),
            'Create URL Rewrite dropdown is not present');
        $this->assertTrue($this->controlIsPresent('button', 'back'), 'Back button is not present');
        $this->assertTrue($this->controlIsPresent(self::UIMAP_TYPE_FIELDSET, 'select_category'),
            'Select Category fieldset is not present');
    }

    /**
     * Verifying Required field for Custom URL rewrite
     *
     * @param string $emptyField
     *
     * @test
     * @dataProvider withRequiredFieldsEmptyDataProvider
     * @TestlinkId TL-MAGE-5518
     */
    public function withRequiredFieldsEmpty($emptyField)
    {
        //Data
        $fieldData = $this->loadDataSet('UrlRewrite', 'url_rewrite_custom', array($emptyField => '%noValue%'));
        //Steps
        $this->navigate('url_rewrite_management');
        $this->urlRewriteHelper()->createUrlRewrite($fieldData);
        //Verifying
        $this->addFieldIdToMessage(self::FIELD_TYPE_INPUT, $emptyField);
        $this->assertMessagePresent(self::MESSAGE_TYPE_VALIDATION, 'empty_required_field');
        $this->assertTrue($this->verifyMessagesCount(), $this->getParsedMessages());
    }

    public function withRequiredFieldsEmptyDataProvider()
    {
        return array(
            array('id_path'),
            array('request_path'),
            array('target_path')
        );
    }

    /**
     * Verifying Required field for Product URl rewrite
     *
     * @test
     * @TestlinkId TL-MAGE-5517
     */
    public function withRequiredFieldsNotEditable()
    {
        //Data
        $productData = $this->loadDataSet('Product', 'simple_product_url_rewrite');
        $productSearch = $this->loadDataSet('Product', 'product_search',
            array('product_sku' => $productData['general_sku']));
        $fieldData = $this->loadDataSet('UrlRewrite', 'url_rewrite_product', array(
            'filter_product_sku' => $productSearch['product_sku'],
            'category' => $productData['general_categories']
        ));
        //Steps
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($productData);
        $this->assertMessagePresent(self::MESSAGE_TYPE_SUCCESS, 'success_saved_product');
        $this->navigate('url_rewrite_management');
        $this->urlRewriteHelper()->createUrlRewrite($fieldData, false);
        //Verifying
        $this->assertFalse($this->controlIsEditable(self::FIELD_TYPE_INPUT, 'id_path'), 'ID Path field is editable');
        $this->assertFalse($this->controlIsEditable(self::FIELD_TYPE_INPUT, 'target_path'),
            'Target Path field is editable');
    }

    /**
     * Verifying Required field for Category URl rewrite
     *
     * @depends withRequiredFieldsNotEditable
     *
     * @test
     * @TestlinkId TL-MAGE-5677
     */
    public function withRequiredFieldsNotEditableForCategory()
    {
        //Data
        $productData = $this->loadDataSet('Product', 'simple_product_url_rewrite');
        $fieldData = $this->loadDataSet('UrlRewrite', 'url_rewrite_category', array(
            'category' => $productData['general_categories']
        ));
        //Steps
        $this->navigate('url_rewrite_management');
        $this->urlRewriteHelper()->createUrlRewrite($fieldData, false);
        //Verifying
        $this->assertFalse($this->controlIsEditable(self::FIELD_TYPE_INPUT, 'id_path'), 'ID Path field is editable');
        $this->assertFalse($this->controlIsEditable(self::FIELD_TYPE_INPUT, 'target_path'),
            'Target Path field is editable');
    }

    /**
     * Verifying Required field for Custom URl rewrite
     *
     * @test
     * @TestlinkId TL-MAGE-5694
     */
    public function withRequiredFieldsNotEditableForCustom()
    {
        //Steps
        $this->navigate('url_rewrite_management');
        $this->clickButton('add_new_rewrite');
        $this->fillDropdown('rewrite_type', 'Custom');
        $this->waitForPageToLoad();
        $this->validatePage();
        //Verifying
        $this->assertTrue($this->controlIsEditable(self::FIELD_TYPE_INPUT, 'id_path'), 'ID Path field is not editable');
        $this->assertTrue($this->controlIsEditable(self::FIELD_TYPE_INPUT, 'target_path'),
            'Target Path field is not editable');
    }

    /**
     * Create URL rewrite for product
     *
     * @return array
     * @test
     * @TestlinkId TL-MAGE-5503
     */
    public function urlRewriteForProduct()
    {
        //Data
        $productData = $this->loadDataSet('Product', 'simple_product_visible');
        $productSearch = $this->loadDataSet('Product', 'product_search',
            array('product_sku' => $productData['general_sku']));
        $fieldData = $this->loadDataSet('UrlRewrite', 'url_rewrite_product', array(
            'filter_product_sku' => $productSearch['product_sku'],
            'category' => $productData['general_categories']
        ));
        //Steps
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($productData);
        $this->assertMessagePresent(self::MESSAGE_TYPE_SUCCESS, 'success_saved_product');
        $this->navigate('url_rewrite_management');
        $this->urlRewriteHelper()->createUrlRewrite($fieldData);
        $rewriteUrl = $this->xmlSitemapHelper()->getFileUrl($fieldData['rewrite_info']['request_path']);
        $this->frontend();
        $this->url($rewriteUrl);
        //Verifying
        $productUrl = trim(strtolower(preg_replace('#[^0-9a-z]+#i', '-', $productData['general_name'])), '-');
        $this->addParameter('productUrl', $productUrl);
        $this->addParameter('elementTitle', $productData['general_name']);
        $this->validatePage('product_page');

        return $fieldData;
    }

    /**
     * Create URL rewrite for product with existing Request path
     *
     * @depends urlRewriteForProduct
     *
     * @test
     * @TestlinkId TL-MAGE-5514
     *
     * @param $rewriteData
     */
    public function urlRewriteForProductExistingReqPath($rewriteData)
    {
        //Data
        $productData = $this->loadDataSet('Product', 'simple_product_visible');
        $productSearch = $this->loadDataSet('Product', 'product_search',
            array('product_sku' => $productData['general_sku']));
        $fieldData = $this->loadDataSet('UrlRewrite', 'url_rewrite_product', array(
            'filter_product_sku' => $productSearch['product_sku'],
            'category' => $productData['general_categories']
        ));
        //Steps
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($productData);
        $this->assertMessagePresent(self::MESSAGE_TYPE_SUCCESS, 'success_saved_product');
        $this->navigate('url_rewrite_management');
        $this->urlRewriteHelper()->createUrlRewrite($fieldData, false);
        $this->fillField('request_path', $rewriteData['rewrite_info']['request_path']);
        $this->saveForm('save');
        //Verifying
        $this->assertMessagePresent('validation', 'req_path_exist');
    }

    /**
     * Custom URl rewrite from CMS Page to the external link
     *
     * @test
     * @TestlinkId TL-MAGE-6123
     */
    public function cmsPageRewriteExtLink()
    {

        //Data
        $pageData = $this->loadDataSet('UrlRewrite', 'url_cms_page_req');
        $rewriteData = $this->loadDataSet('UrlRewrite', 'url_rewrite_custom', array(
            'id_path' => $pageData['page_information']['url_key'],
            'store' => $pageData['page_information']['store_view'],
            'request_path' => $pageData['page_information']['url_key'],
            'target_path' => 'http://magentocommerce.com'
        ));
        //Steps
        $this->navigate('manage_stores');
        $this->navigate('manage_cms_pages');
        $this->cmsPagesHelper()->createCmsPage($pageData);
        $this->assertMessagePresent(self::MESSAGE_TYPE_SUCCESS, 'success_saved_cms_page');
        $this->navigate('url_rewrite_management');
        $this->urlRewriteHelper()->createUrlRewrite($rewriteData);
        $this->assertMessagePresent(self::MESSAGE_TYPE_SUCCESS, 'success_saved_url_rewrite');
        $rewriteUrl = $this->xmlSitemapHelper()->getFileUrl($rewriteData['rewrite_info']['request_path']);
        $this->frontend();
        $this->url($rewriteUrl);
        //Verifying
        $this->assertSame($this->title(), 'Ecommerce Software & Ecommerce Platform Solutions | Magento',
            'Wrong page is opened');
    }

    /**
     * Custom URl rewrite for CMS Page
     *
     * @return array
     * @test
     * @TestlinkId TL-MAGE-6049
     */
    public function withRequiredFieldsCmsPageRewrite()
    {
        //Create data
        $pageData = $this->loadDataSet('UrlRewrite', 'url_cms_page_req');
        $customData = $this->loadDataSet('UrlRewrite', 'url_rewrite_custom_sample');
        $rewriteData = $this->loadDataSet('UrlRewrite', 'url_rewrite_custom', array(
            'id_path' => $pageData['page_information']['url_key'],
            'store' => $pageData['page_information']['store_view'],
            'request_path' => $pageData['page_information']['url_key'],
            'target_path' => $customData['target_path']
        ));
        //Steps
        $this->navigate('manage_cms_pages');
        $this->cmsPagesHelper()->createCmsPage($pageData);
        $this->assertMessagePresent(self::MESSAGE_TYPE_SUCCESS, 'success_saved_cms_page');
        $this->admin('url_rewrite_management');
        $this->urlRewriteHelper()->createUrlRewrite($rewriteData);
        $this->assertMessagePresent(self::MESSAGE_TYPE_SUCCESS, 'success_saved_url_rewrite');
        $rewriteUrl = $this->xmlSitemapHelper()->getFileUrl($pageData['page_information']['url_key']);
        $this->frontend();
        $this->url($rewriteUrl);
        //Verifying
        $this->assertSame($this->title(), 'Customer Service', 'Wrong page is opened');

        return $pageData;
    }

    /**
     * URL Rewrites using an external link
     *
     * @param array $data
     *
     * @test
     * @depends withRequiredFieldsCmsPageRewrite
     * @TestlinkId TL-MAGE-5507
     */
    public function withRequiredFieldsRewriteExtLink($data)
    {
        //Data
        $pageData = $this->loadDataSet('UrlRewrite', 'url_search_url',
            array('url_key' => $data['page_information']['url_key']));
        //Steps
        $this->navigate('url_rewrite_management');
        $this->urlRewriteHelper()->openUrlRewrite(array($pageData['url_key']));
        $this->fillField('target_path', 'http://google.com');
        $this->saveForm('save');
        $this->assertMessagePresent(self::MESSAGE_TYPE_SUCCESS, 'success_saved_url_rewrite');
        $this->flushCache();
        $rewriteUrl = $this->xmlSitemapHelper()->getFileUrl($pageData['url_key']);
        $this->frontend();
        $this->url($rewriteUrl);
        //Verifying
        $this->assertSame($this->title(), 'Google', 'Wrong page is opened');
    }

    /**
     * Product URL rewrite created for the one store shouldn't work for other store
     *
     * @test
     * @TestlinkId TL-MAGE-5508
     */
    public function productRewriteOfOneStore()
    {
        //Data
        $productData = $this->loadDataSet('Product', 'simple_product_url_rewrite');
        $category = $this->loadDataSet('Category', 'root_category_required');
        $storeData = $this->loadDataSet('Store', 'generic_store', array('root_category' => $category['name']));
        $storeViewData = $this->loadDataSet('StoreView', 'generic_store_view',
            array('store_name' => $storeData['store_name']));
        //Steps
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($productData);
        $this->assertMessagePresent(self::MESSAGE_TYPE_SUCCESS, 'success_saved_product');
        //Create Category
        $this->navigate('manage_categories', false);
        $this->categoryHelper()->checkCategoriesPage();
        $this->categoryHelper()->createCategory($category);
        $this->assertMessagePresent(self::MESSAGE_TYPE_SUCCESS, 'success_saved_category');
        //Create Store and Store View
        $this->navigate('manage_stores');
        $this->storeHelper()->createStore($storeData, 'store');
        $this->assertMessagePresent(self::MESSAGE_TYPE_SUCCESS, 'success_saved_store');
        $this->storeHelper()->createStore($storeViewData, 'store_view');
        $this->assertMessagePresent(self::MESSAGE_TYPE_SUCCESS, 'success_saved_store_view');
        //Generate request path and open it
        $url = str_replace(array('(', ')'), array('-', ''), $productData['autosettings_url_key']) . '.html';
        $this->addParameter('url_key', $url);
        $this->addParameter('elementTitle', $productData['general_name']);
        $this->frontend('test_page');
        $this->loginAdminUser();
        $this->reindexInvalidedData();
        //Select other store
        $this->frontend();
        $this->addParameter('store', $storeData['store_name']);
        $this->addParameter('storeViewCode', $storeViewData['store_view_code']);
        $this->clickControl(self::FIELD_TYPE_LINK, 'select_store', false);
        $this->waitForPageToLoad();
        $this->frontend('test_page');
        $rewrittenUrl = $productData['autosettings_url_key'];
        $this->addParameter('url_key', $rewrittenUrl);
        $this->addParameter('elementTitle', $productData['general_name']);
        $this->url($rewrittenUrl);
        //Verifying
        $this->assertSame($this->title(), '404 Not Found 1', 'Wrong page is opened');
    }

    /**
     * Category URL rewrite for the same store
     *
     * @return array
     * @test
     * @TestlinkId TL-MAGE-5515
     */
    public function urlRewriteCategory()
    {
        //Data
        $categoryData = $this->loadDataSet('Category', 'sub_category_required');
        $fieldData = $this->loadDataSet('UrlRewrite', 'url_rewrite_category', array(
            'category' => $categoryData['parent_category'] . '/' . $categoryData['name']
        ));
        //Created category
        $this->navigate('manage_categories', false);
        $this->categoryHelper()->checkCategoriesPage();
        $this->categoryHelper()->createCategory($categoryData);
        $this->assertMessagePresent('success', 'success_saved_category');
        //Open URL rewrite management
        $this->navigate('url_rewrite_management');
        $this->urlRewriteHelper()->createUrlRewrite($fieldData);
        $this->assertMessagePresent(self::MESSAGE_TYPE_SUCCESS, 'success_saved_url_rewrite');
        $rewriteUrl = $this->xmlSitemapHelper()->getFileUrl($fieldData['rewrite_info']['request_path']);
        $this->frontend();
        $this->url($rewriteUrl);
        //Verifying
        $this->assertSame($this->title(), $categoryData['name'], 'Wrong page is opened');

        return $fieldData;
    }

    /**
     * URL Rewrite for category not available from other store
     *
     * @param $fieldData
     *
     * @test
     * @depends urlRewriteCategory
     * @TestlinkId TL-MAGE-5516
     */
    public function categoryUrlRewriteOtherStore($fieldData)
    {
        //Data
        $category = $this->loadDataSet('Category', 'root_category_required');
        $storeData = $this->loadDataSet('Store', 'generic_store', array('root_category' => $category['name']));
        $storeViewData = $this->loadDataSet('StoreView', 'generic_store_view',
            array('store_name' => $storeData['store_name']));
        //Steps
        $this->navigate('manage_categories', false);
        $this->categoryHelper()->checkCategoriesPage();
        $this->categoryHelper()->createCategory($category);
        $this->assertMessagePresent(self::MESSAGE_TYPE_SUCCESS, 'success_saved_category');
        //Create Store and Store View
        $this->navigate('manage_stores');
        $this->storeHelper()->createStore($storeData, 'store');
        $this->assertMessagePresent(self::MESSAGE_TYPE_SUCCESS, 'success_saved_store');
        $this->storeHelper()->createStore($storeViewData, 'store_view');
        $this->assertMessagePresent(self::MESSAGE_TYPE_SUCCESS, 'success_saved_store_view');
        $this->reindexInvalidedData();
        //Select other store
        $this->frontend();
        $this->addParameter('store', $storeData['store_name']);
        $this->addParameter('storeViewCode', $storeViewData['store_view_code']);
        $this->clickControl(self::FIELD_TYPE_LINK, 'select_store', false);
        $rewriteUrl = $this->xmlSitemapHelper()->getFileUrl($fieldData['rewrite_info']['request_path']);
        //Opening URL rewrite on selected store
        $this->url($rewriteUrl);
        $this->assertEquals('404 Not Found 1', $this->title());
    }

    /**
     * URL Rewrite for product in scope of two different Websites
     *
     * @test
     * @TestlinkId TL-MAGE-5510
     */
    public function productRewriteToOtherWebsite()
    {
        //Data
        $category = $this->loadDataSet('Category', 'root_category_required');
        $websiteDataOne = $this->loadDataSet('Website', 'generic_website');
        $storeData = $this->loadDataSet('Store', 'generic_store',
            array('website' => $websiteDataOne['website_name'], 'root_category' => $category['name']));
        $storeViewData = $this->loadDataSet('StoreView', 'generic_store_view',
            array('store_name' => $storeData['store_name']));
        $productData = $this->loadDataSet('Product', 'simple_product_url_rewrite',
            array('websites' => $websiteDataOne['website_name'], 'general_categories' => $category['name']));
        //Steps
        //Create Root for Website 2
        $this->navigate('manage_categories', false);
        $this->categoryHelper()->checkCategoriesPage();
        $this->categoryHelper()->createCategory($category);
        $this->assertMessagePresent(self::MESSAGE_TYPE_SUCCESS, 'success_saved_category');
        // Crete Website 2 with Store and Store View
        $this->navigate('manage_stores');
        $this->storeHelper()->createStore($websiteDataOne, 'website');
        $this->assertMessagePresent(self::MESSAGE_TYPE_SUCCESS, 'success_saved_website');
        $this->storeHelper()->createStore($storeData, 'store');
        $this->assertMessagePresent(self::MESSAGE_TYPE_SUCCESS, 'success_saved_store');
        $this->storeHelper()->createStore($storeViewData, 'store_view');
        $this->assertMessagePresent(self::MESSAGE_TYPE_SUCCESS, 'success_saved_store_view');
        //Create product and assign to Website2
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($productData);
        $this->assertMessagePresent(self::MESSAGE_TYPE_SUCCESS, 'success_saved_product');
        $url = str_replace(array('(', ')'), array('-', ''), $productData['autosettings_url_key']) . '.html';
        $this->frontend();
        $this->url($url);
        //Verifying
        $this->assertSame($this->title(), '404 Not Found 1', 'Wrong page is opened');
    }

    /**
     * URL Rewrite for category in scope of two different Websites
     *
     * @test
     * @TestlinkId TL-MAGE-5533
     */

    public function forCategoryLinkToOtherWebsite()
    {
        //Data
        $category = $this->loadDataSet('Category', 'root_category_required');
        $categoryData = $this->loadDataSet('Category', 'sub_category_required_url_rewrite',
            array('parent_category' => $category['name'], 'url_key' => 'testCategory'));
        $websiteDataOne = $this->loadDataSet('Website', 'generic_website');
        $storeData = $this->loadDataSet('Store', 'generic_store',
            array('website' => $websiteDataOne['website_name'], 'root_category' => $category['name']));
        $storeViewData = $this->loadDataSet('StoreView', 'generic_store_view',
            array('store_name' => $storeData['store_name']));
        //Steps
        //Create Root Category for Website 2
        $this->navigate('manage_categories', false);
        $this->categoryHelper()->checkCategoriesPage();
        $this->categoryHelper()->createCategory($category);
        $this->assertMessagePresent(self::MESSAGE_TYPE_SUCCESS, 'success_saved_category');
        //Create SubCategory
        $this->categoryHelper()->createCategory($categoryData);
        $this->assertMessagePresent(self::MESSAGE_TYPE_SUCCESS, 'success_saved_category');
        // Crete Website 2 with Store and Store View
        $this->navigate('manage_stores');
        $this->storeHelper()->createStore($websiteDataOne, 'website');
        $this->assertMessagePresent(self::MESSAGE_TYPE_SUCCESS, 'success_saved_website');
        $this->storeHelper()->createStore($storeData, 'store');
        $this->assertMessagePresent(self::MESSAGE_TYPE_SUCCESS, 'success_saved_store');
        $this->storeHelper()->createStore($storeViewData, 'store_view');
        $this->assertMessagePresent(self::MESSAGE_TYPE_SUCCESS, 'success_saved_store_view');
        //Generate request path and open it
        $url = str_replace(array('(', ')'), array('-', ''), $categoryData['url_key']) . '.html';
        //Open product URl
        $this->frontend();
        $this->url($url);
        //Verifying
        $this->assertSame($this->title(), '404 Not Found 1', 'Wrong page is opened');
    }

    /**
     * Custom URL Rewrite for product in scope of the same one store
     *
     * @return string
     * @test
     * @TestlinkId TL-MAGE-5565
     */
    public function customProductUrlRewriteSameStore()
    {
        //Data
        $productData = $this->loadDataSet('Product', 'simple_product_url_rewrite');
        $url = str_replace(array('(', ')'), array('-', ''), $productData['autosettings_url_key']) . '.html';
        $fieldData = $this->loadDataSet('UrlRewrite', 'url_rewrite_custom',
            array('target_path' => $url)
        );
        //Steps
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($productData);
        $this->assertMessagePresent(self::MESSAGE_TYPE_SUCCESS, 'success_saved_product');
        $this->navigate('url_rewrite_management');
        $this->urlRewriteHelper()->createUrlRewrite($fieldData);
        $this->assertMessagePresent(self::MESSAGE_TYPE_SUCCESS, 'success_saved_url_rewrite');
        $rewriteUrl = $this->xmlSitemapHelper()->getFileUrl($url);
        $this->frontend();
        $this->url($rewriteUrl);
        //Verifying
        $this->assertSame($this->title(), $productData['general_name'], 'Wrong page is opened');

        return ($url);
    }

    /**
     * Custom product URL rewrite created for the same one store should not work for other store
     *
     * @param string $url
     *
     * @test
     * @depends customProductUrlRewriteSameStore
     * @TestlinkId TL-MAGE-5571
     */
    public function customProductUrlRewriteOtherStore($url)
    {
        //Data
        $category = $this->loadDataSet('Category', 'root_category_required');
        $storeData = $this->loadDataSet('Store', 'generic_store', array('root_category' => $category['name']));
        $storeViewData = $this->loadDataSet('StoreView', 'generic_store_view',
            array('store_name' => $storeData['store_name']));
        //Steps
        $this->navigate('manage_categories', false);
        $this->categoryHelper()->checkCategoriesPage();
        $this->categoryHelper()->createCategory($category);
        $this->assertMessagePresent(self::MESSAGE_TYPE_SUCCESS, 'success_saved_category');
        //Create Store and Store View
        $this->navigate('manage_stores');
        $this->storeHelper()->createStore($storeData, 'store');
        $this->assertMessagePresent(self::MESSAGE_TYPE_SUCCESS, 'success_saved_store');
        $this->storeHelper()->createStore($storeViewData, 'store_view');
        $this->assertMessagePresent(self::MESSAGE_TYPE_SUCCESS, 'success_saved_store_view');
        //Select other store
        $this->frontend();
        $this->addParameter('store', $storeData['store_name']);
        $this->addParameter('storeViewCode', $storeViewData['store_view_code']);
        $this->clickControl(self::FIELD_TYPE_LINK, 'select_store', false);
        $this->waitForPageToLoad();
        $rewriteUrl = $this->xmlSitemapHelper()->getFileUrl($url);
        $this->frontend();
        $this->url($rewriteUrl);
        //Verifying
        $this->assertSame($this->title(), '404 Not Found 1', 'Wrong page is opened');
    }

    /**
     * URL Rewrite for product in scope of two different Websites
     *
     * @test
     * @TestlinkId TL-MAGE-5512
     */
    public function productRewriteToOtherStore()
    {
        //Data
        $category = $this->loadDataSet('Category', 'root_category_required');
        $storeData = $this->loadDataSet('Store', 'generic_store', array('root_category' => $category['name']));
        $storeViewData = $this->loadDataSet('StoreView', 'generic_store_view',
            array('store_name' => $storeData['store_name']));
        $productData = $this->loadDataSet('Product', 'simple_product_url_rewrite',
            array('general_categories' => $category['name']));
        $urlRewriteData = $this->loadDataSet('UrlRewrite', 'url_rewrite_product', array(
            'filter_product_sku' => $productData['general_sku'],
            'category' => $productData['general_categories']
        ));
        //Steps
        //Create Root for Store 2
        $this->navigate('manage_categories', false);
        $this->categoryHelper()->checkCategoriesPage();
        $this->categoryHelper()->createCategory($category);
        $this->assertMessagePresent(self::MESSAGE_TYPE_SUCCESS, 'success_saved_category');
        $this->navigate('manage_stores');
        // Crete Store 2 and Store View 2
        $this->storeHelper()->createStore($storeData, 'store');
        $this->assertMessagePresent(self::MESSAGE_TYPE_SUCCESS, 'success_saved_store');
        $this->storeHelper()->createStore($storeViewData, 'store_view');
        $this->assertMessagePresent(self::MESSAGE_TYPE_SUCCESS, 'success_saved_store_view');
        //Create product and assign to Website2
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($productData);
        $this->assertMessagePresent(self::MESSAGE_TYPE_SUCCESS, 'success_saved_product');
        $this->admin('url_rewrite_management');
        $this->urlRewriteHelper()->createUrlRewrite($urlRewriteData, false);
        //Check that 'Default Store View' isn\t present in request store
        $options = $this->select($this->getControlElement(self::FIELD_TYPE_DROPDOWN, 'filter_store_view'))
            ->selectOptionLabels();
        $this->assertFalse(in_array('Default Store View', $options),
            'Option with value "Default Store View" is present in "request_store" dropdown');
    }
}
