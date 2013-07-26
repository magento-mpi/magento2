<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_UrlRewrite
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Url Rewrite Admin Page
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_UrlRewrite_CreateTest extends Mage_Selenium_TestCase
{
    public function setUpBeforeTests()
    {
        $this->markTestIncomplete('MAGETWO-11231');
    }

    protected function assertPreConditions()
    {
        $this->loginAdminUser();
    }

    protected function tearDownAfterTestClass()
    {
        $this->markTestIncomplete('MAGETWO-6965');
        $this->frontend();
        $this->addParameter('store', 'Main Website Store');
        $this->clickControl('link', 'select_store', false);
        $this->validatePage();
    }

    /**
     * <p>Verify that url rewrite form is present at the backend page<p>
     *
     * @test
     */
    public function testFormIsPresent()
    {
        //Steps
        $this->navigate('url_rewrite_management');
        $this->clickButton('add_new_rewrite');
        //Verifying
        $this->assertTrue($this->controlIsPresent('dropdown', 'create_url_rewrite_dropdown'),
            'Create URL Rewrite dropdown is not present');
        $this->assertTrue($this->controlIsPresent('button', 'back'), 'Back button is not present');
        $this->assertTrue($this->controlIsPresent('fieldset', 'select_category'),
            'Select Category fieldset is not present');
    }

    /**
     * <p>Verifying Required field for Custom URL rewrite</p>
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
        //Open URL rewrite management page
        $this->navigate('url_rewrite_management');
        //Click 'Add new rewrite' button
        $this->clickButton('add_new_rewrite');
        //At Create URL rewrite dropdown select Custom
        $this->fillDropdown('create_url_rewrite_dropdown', 'Custom');
        $this->waitForPageToLoad();
        $this->validatePage();
        //Fill required fields except one
        $this->fillForm($fieldData);
        //Click Save button
        $this->saveForm('save');
        //Verifying
        $this->addFieldIdToMessage('field', $emptyField);
        $this->assertMessagePresent('validation', 'empty_required_field');
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
     * <p>Verifying Required field for Product URl rewrite</p>
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
        //Create Simple Product
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($productData);
        $this->assertMessagePresent('success', 'success_saved_product');
        //Open Manage URL rewrite page
        $this->admin('url_rewrite_management');
        //Click 'Add new rewrite' button
        $this->clickButton('add_new_rewrite');
        //Select "For Product"
        $this->fillDropdown('create_url_rewrite_dropdown', 'For product');
        $this->waitForPageToLoad();
        //Find product in the Grid and open it
        $this->validatePage('add_new_urlrewrite_product');
        $this->searchAndOpen($productSearch, 'product_rewrite', false);
        $this->waitForPageToLoad();
        $this->addParameter('id', $this->defineParameterFromUrl('product'));
        $this->validatePage();
        //Select Category
        $this->addParameter('rootName', $productData['general_categories']);
        $this->clickControl('link', 'root_category', false);
        $this->waitForPageToLoad();
        $this->addParameter('categoryId', $this->defineParameterFromUrl('category'));
        $this->validatePage();
        //Check fields id_path & target path isn't editable
        $this->assertFalse($this->controlIsEditable('field', 'id_path'), 'ID Path field is editable');
        $this->assertFalse($this->controlIsEditable('field', 'target_path'), 'Target Path field is editable');
    }

    /**
     * <p>Verifying Required field for Product URl rewrite</p>
     *
     * @depends withRequiredFieldsNotEditable
     *
     * @test
     * @TestlinkId TL-MAGE-5677
     */
    public function withRequiredFieldsNotEditableForCategory()
    {
        $productData = $this->loadDataSet('Product', 'simple_product_url_rewrite');
        //Open Manage URL rewrite page
        $this->admin('url_rewrite_management');
        //Click 'Add new rewrite' button
        $this->clickButton('add_new_rewrite');
        //Select "For category"
        $this->fillDropdown('create_url_rewrite_dropdown', 'For category');
        $this->waitForPageToLoad();
        //Select Category
        $this->validatePage('add_new_urlrewrite_category');
        $this->addParameter('rootName', $productData['general_categories']);
        $this->clickControl('link', 'root_category', false);
        $this->waitForPageToLoad();
        $this->addParameter('id', $this->defineParameterFromUrl('category'));
        $this->validatePage('edit_urlrewrite_category');
        //Check fields id_path & target path isn't editable
        $this->assertFalse($this->controlIsEditable('field', 'id_path'), 'ID Path field is editable');
        $this->assertFalse($this->controlIsEditable('field', 'target_path'), 'Target Path field is editable');
    }

    /**
     * <p>Verifying Required field for Custom URl rewrite</p>
     *
     * @test
     * @TestlinkId TL-MAGE-5694
     */
    public function withRequiredFieldsNotEditableForCustom()
    {
        //Open Custom page
        $this->admin('url_rewrite_management');
        $this->clickButton('add_new_rewrite');
        $this->fillDropdown('create_url_rewrite_dropdown', 'Custom');
        $this->waitForPageToLoad();
        $this->validatePage();
        //Check fields id_path & target path is editable
        $this->assertTrue($this->controlIsEditable('field', 'id_path'), 'ID Path field is not editable');
        $this->assertTrue($this->controlIsEditable('field', 'target_path'), 'Target Path field is not editable');
    }

    /**
     * <p>Create URL rewrite for product</p>
     *
     * @return array
     * @test
     * @TestlinkId TL-MAGE-5503
     */
    public function urlRewriteForProduct()
    {
        $this->markTestIncomplete('MAGETWO-6965');
        //Data
        $fieldData = $this->loadDataSet('UrlRewrite', 'url_rewrite_product');
        $product = $this->loadDataSet('Product', 'simple_product_visible');
        $productSearch = $this->loadDataSet('Product', 'product_search',
            array('product_sku' => $product['general_sku']));
        //Create Simple Product
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($product);
        $this->assertMessagePresent('success', 'success_saved_product');
        //Open Manage URL rewrite page
        $this->admin('url_rewrite_management');
        //Click 'Add new rewrite' button
        $this->clickButton('add_new_rewrite');
        //Select "For Product"
        $this->fillDropdown('create_url_rewrite_dropdown', 'For product');
        $this->waitForPageToLoad();
        //Find product in the Grid and open it
        $this->validatePage('add_new_urlrewrite_product');
        $this->searchAndOpen($productSearch, 'product_rewrite', false);
        $this->waitForPageToLoad();
        $this->addParameter('id', $this->defineParameterFromUrl('product'));
        $this->validatePage();
        //Select Category
        $this->addParameter('rootName', $product['general_categories']);
        $this->clickControl('link', 'root_category', false);
        $this->waitForPageToLoad();
        $this->addParameter('categoryId', $this->defineParameterFromUrl('category'));
        $this->validatePage();
        //Fill request path input field
        $this->fillField('request_path', $fieldData['request_path']);
        //Click Save button
        $this->saveForm('save');
        $this->assertMessagePresent('success', 'success_saved_url_rewrite');
        //Generating URL rewrite link
        $rewriteUrl = $this->xmlSitemapHelper()->getFileUrl($fieldData['request_path']);
        //Open page on frontend
        $this->frontend();
        $this->url($rewriteUrl);
        //Verifying page of URL rewrite for product
        $productUrl = trim(strtolower(preg_replace('#[^0-9a-z]+#i', '-', $product['general_name'])), '-');
        $this->addParameter('productUrl', $productUrl);
        $this->addParameter('elementTitle', $product['general_name']);
        $this->validatePage('product_page');

        return $fieldData;
    }

    /**
     * <p>Create URL rewrite for product with existing Request path</p>
     *
     * @depends urlRewriteForProduct
     *
     * @test
     * @TestlinkId TL-MAGE-5514
     */
    public function urlRewriteForProductExistingReqPath($fieldData)
    {
        $productData = $this->loadDataSet('Product', 'simple_product_visible');
        $productSearch = $this->loadDataSet('Product', 'product_search',
            array('product_sku' => $productData['general_sku']));
        //Create Simple Product
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($productData);
        $this->assertMessagePresent('success', 'success_saved_product');
        //Open Manage URL rewrite page
        $this->admin('url_rewrite_management');
        //Click 'Add new rewrite' button
        $this->clickButton('add_new_rewrite');
        //Select "For Product"
        $this->fillDropdown('create_url_rewrite_dropdown', 'For product');
        $this->waitForPageToLoad();
        //Find product in the Grid and open it
        $this->validatePage('add_new_urlrewrite_product');
        $this->searchAndOpen($productSearch, 'product_rewrite', false);
        $this->waitForPageToLoad();
        $this->addParameter('id', $this->defineParameterFromUrl('product'));
        $this->validatePage();
        //Select Category
        $this->addParameter('rootName', $productData['general_categories']);
        $this->clickControl('link', 'root_category', false);
        $this->waitForPageToLoad();
        $this->addParameter('categoryId', $this->defineParameterFromUrl('category'));
        $this->validatePage();
        //Fill request path input field with existing data
        $this->fillField('request_path', $fieldData['request_path']);
        //Click Save button and Check that validation message appear
        $this->saveForm('save');
        $this->assertMessagePresent('validation', 'req_path_exist');
    }

    /**
     * <p>CMS Pages custom URL Rewrites</p>
     *
     * @test
     * @TestlinkId TL-MAGE-6123
     */
    public function cmsPageRewriteExtLink()
    {
        $this->markTestIncomplete('MAGETWO-3263');
        $pageData = $this->loadDataSet('UrlRewrite', 'url_cms_page_req');
        //Create data
        $this->navigate('manage_stores');
        $this->storeHelper()->createStore('StoreView/generic_store_view', 'store_view');
        $this->assertMessagePresent('success', 'success_saved_store_view');
        //Create CMS Page
        $this->navigate('manage_cms_pages');
        $this->cmsPagesHelper()->createCmsPage($pageData);
        $this->assertMessagePresent('success', 'success_saved_cms_page');
        //Create Custom URL rewrite
        $this->admin('url_rewrite_management');
        $this->clickButton('add_new_rewrite');
        $this->fillDropdown('create_url_rewrite_dropdown', 'Custom');
        $this->waitForPageToLoad();
        $this->validatePage();
        //Fill form and save sitemap
        $this->fillField('target_path', 'http://magentocommerce.com');
        $this->fillField('id_path', $pageData['page_information']['url_key']);
        $this->fillField('request_path', $pageData['page_information']['url_key']);
        $this->saveForm('save');
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_url_rewrite');
        //Generate request path
        $rewriteUrl = $this->xmlSitemapHelper()->getFileUrl($pageData['page_information']['url_key']);
        //Open page on frontend
        $this->frontend();
        $this->url($rewriteUrl);
        //Verifying page of URL rewrite for product
        $this->assertSame($this->title(), 'Magento - Home - eCommerce Software for Growth', 'Wrong page is opened');
    }

    /**
     * <p>CMS Pages custom URL Rewrites</p>
     *
     * @return array
     * @test
     * @TestlinkId TL-MAGE-6049
     */
    public function withRequiredFieldsCmsPageRewrite()
    {
        //Create data
        $productData = $this->loadDataSet('UrlRewrite', 'url_rewrite_custom_sample');
        $pageData = $this->loadDataSet('UrlRewrite', 'url_cms_page_req');
        //Create CMS Page
        $this->navigate('manage_cms_pages');
        $this->cmsPagesHelper()->createCmsPage($pageData);
        $this->assertMessagePresent('success', 'success_saved_cms_page');
        //Create Custom URL rewrite
        $this->admin('url_rewrite_management');
        $this->clickButton('add_new_rewrite');
        $this->fillDropdown('create_url_rewrite_dropdown', 'Custom');
        $this->waitForPageToLoad();
        $this->validatePage();
        //Fill form and save sitemap
        $this->fillFieldset($productData, 'custom_rewrite');
        $this->fillField('id_path', $pageData['page_information']['url_key']);
        $this->fillField('request_path', $pageData['page_information']['url_key']);
        $this->saveForm('save');
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_url_rewrite');
        //Generate request path
        $rewriteUrl = $this->xmlSitemapHelper()->getFileUrl($pageData['page_information']['url_key']);
        //Open page on frontend
        $this->frontend();
        $this->url($rewriteUrl);
        //Verifying page of URL rewrite for product
        $this->assertSame($this->title(), 'Customer Service', 'Wrong page is opened');

        return $pageData;
    }

    /**
     * <p>URL Rewrites using an external link</p>
     *
     * @param array $data
     *
     * @test
     * @depends withRequiredFieldsCmsPageRewrite
     * @TestlinkId TL-MAGE-5507
     */
    public function withRequiredFieldsRewriteExtLink($data)
    {
        //Create data
        $pageData = $this->loadDataSet('UrlRewrite', 'url_search_url',
            array('url_key' => $data['page_information']['url_key']));

        $this->navigate('url_rewrite_management');
        $this->validatePage();
        $this->searchAndOpen($pageData, 'url_rewrite_grid');
        $this->addParameter('id', $this->defineParameterFromUrl('customId'));
        //Fill form and save sitemap
        $this->fillField('target_path', 'http://google.com');
        $this->saveForm('save');
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_url_rewrite');
        //Flush Magento cache
        $this->flushCache();
        //Generate request path
        $rewriteUrl = $this->xmlSitemapHelper()->getFileUrl($pageData['url_key']);
        //Open page on frontend
        $this->frontend();
        $this->url($rewriteUrl);
        //Verifying page of URL rewrite for product
        $this->assertSame($this->title(), 'Google', 'Wrong page is opened');
    }

    /**
     * <p>URL Rewrites using an external link</p>
     *
     * @test
     * @TestlinkId TL-MAGE-5508
     */
    public function productRewriteOfOneStore()
    {
        $this->markTestIncomplete('MAGETWO-6995');
        $productData = $this->loadDataSet('Product', 'simple_product_url_rewrite');
        $category = $this->loadDataSet('Category', 'root_category_required');
        $storeData = $this->loadDataSet('Store', 'generic_store', array('root_category' => $category['name']));
        $storeViewData = $this->loadDataSet('StoreView', 'generic_store_view',
            array('store_name' => $storeData['store_name']));
        //Create Simple Product
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($productData);
        $this->assertMessagePresent('success', 'success_saved_product');
        //Create Category
        $this->navigate('manage_categories', false);
        $this->categoryHelper()->checkCategoriesPage();
        $this->categoryHelper()->createCategory($category);
        $this->assertMessagePresent('success', 'success_saved_category');
        //Create Store and Store View
        $this->navigate('manage_stores');
        $this->storeHelper()->createStore($storeData, 'store');
        $this->assertMessagePresent('success', 'success_saved_store');
        $this->storeHelper()->createStore($storeViewData, 'store_view');
        $this->assertMessagePresent('success', 'success_saved_store_view');
        //Generate request path and open it
        $url = str_replace(array('(', ')'), array('-', ''), $productData['general_url_key']) . '.html';
        $this->addParameter('url_key', $url);
        $this->addParameter('elementTitle', $productData['general_name']);
        $this->frontend('test_page');
        //Select other store
        $this->frontend();
        $this->addParameter('store', $storeData['store_name']);
        $this->addParameter('storeViewCode', $storeViewData['store_view_code']);
        $this->clickControl('link', 'select_store', false);
        $this->waitForPageToLoad();
        $this->addParameter('elementTitle', '404 Not Found 1');
        $this->frontend('test_page');
    }

    /**
     * <p>URL Rewrite for category in scope of the same one store</p>
     *
     * @return array
     * @test
     * @TestlinkId TL-MAGE-5515
     */
    public function urlRewriteCategory()
    {
        //Data
        $categoryData = $this->loadDataSet('Category', 'sub_category_required');
        $fieldData = $this->loadDataSet('UrlRewrite', 'url_rewrite_category');
        //Created category
        $this->navigate('manage_categories', false);
        $this->categoryHelper()->checkCategoriesPage();
        $this->categoryHelper()->createCategory($categoryData);
        $this->assertMessagePresent('success', 'success_saved_category');
        //Open URL rewrite management
        $this->navigate('url_rewrite_management');
        //Click 'Add new rewrite' button
        $this->clickButton('add_new_rewrite');
        //At Create URL rewrite dropdown select For category
        $this->fillDropdown('create_url_rewrite_dropdown', 'For category');
        //Select Subcategory by name and detect it's id from url
        $this->addParameter('subName', $categoryData['name']);
        $this->clickControl('link', 'sub_category', false);
        $this->waitForPageToLoad();
        $this->addParameter('id', $this->defineParameterFromUrl('category'));
        $this->validatePage();
        //Fill request path input field
        $this->fillField('request_path', $fieldData['request_path']);
        //Click Save button
        $this->saveForm('save');
        $this->assertMessagePresent('success', 'success_saved_url_rewrite');
        //Generating URL rewrite link
        $rewriteUrl = $this->xmlSitemapHelper()->getFileUrl($fieldData['request_path']);
        //Open default store on frontend if opened other one
        $this->frontend();
        //Open URL rewrite for category on frontend
        $this->url($rewriteUrl);
        //Verifying page of URL rewrite for category
        $this->assertSame($this->title(), $categoryData['name'], 'Wrong page is opened');
        return $fieldData;
    }

    /**
     * <p>URL Rewrite for category in scope of the same one store (URL not available from other store)<p>
     *
     * @param $fieldData
     *
     * @test
     * @depends urlRewriteCategory
     * @TestlinkId TL-MAGE-5516
     */
    public function categoryUrlRewriteOtherStore($fieldData)
    {
        $this->markTestIncomplete('MAGETWO-6995');
        $category = $this->loadDataSet('Category', 'root_category_required');
        $storeData = $this->loadDataSet('Store', 'generic_store', array('root_category' => $category['name']));
        $storeViewData = $this->loadDataSet('StoreView', 'generic_store_view',
            array('store_name' => $storeData['store_name']));
        //Create Category
        $this->navigate('manage_categories', false);
        $this->categoryHelper()->checkCategoriesPage();
        $this->categoryHelper()->createCategory($category);
        $this->assertMessagePresent('success', 'success_saved_category');
        //Create Store and Store View
        $this->navigate('manage_stores');
        $this->storeHelper()->createStore($storeData, 'store');
        $this->assertMessagePresent('success', 'success_saved_store');
        $this->storeHelper()->createStore($storeViewData, 'store_view');
        $this->assertMessagePresent('success', 'success_saved_store_view');
        //Select other store
        $this->frontend();
        $this->addParameter('store', $storeData['store_name']);
        $this->addParameter('storeViewCode', $storeViewData['store_view_code']);
        $this->clickControl('link', 'select_store', false);
        $rewriteUrl = $this->xmlSitemapHelper()->getFileUrl($fieldData['request_path']);
        //Opening URL rewrite on selected store
        $this->url($rewriteUrl);
        $this->assertSame($this->title(), '404 Not Found 1', 'Wrong page is opened');
    }

    /**
     * <p>URL Rewrite for product in scope of two different Websites</p>
     *
     * @test
     * @TestlinkId TL-MAGE-5510
     */
    public function productRewriteToOtherWebsite()
    {
        $category = $this->loadDataSet('Category', 'root_category_required');
        $websiteDataOne = $this->loadDataSet('Website', 'generic_website');
        $storeData = $this->loadDataSet('Store', 'generic_store',
            array('website' => $websiteDataOne['website_name'], 'root_category' => $category['name']));
        $storeViewData = $this->loadDataSet('StoreView', 'generic_store_view',
            array('store_name' => $storeData['store_name']));
        $productData = $this->loadDataSet('Product', 'simple_product_url_rewrite',
            array('websites' => $websiteDataOne['website_name'], 'general_categories' => $category['name']));
        //Create Root for Website 2
        $this->navigate('manage_categories', false);
        $this->categoryHelper()->checkCategoriesPage();
        $this->categoryHelper()->createCategory($category);
        $this->assertMessagePresent('success', 'success_saved_category');
        // Crete Website 2 with Store and Store View
        $this->navigate('manage_stores');
        $this->storeHelper()->createStore($websiteDataOne, 'website');
        $this->assertMessagePresent('success', 'success_saved_website');
        $this->storeHelper()->createStore($storeData, 'store');
        $this->assertMessagePresent('success', 'success_saved_store');
        $this->storeHelper()->createStore($storeViewData, 'store_view');
        $this->assertMessagePresent('success', 'success_saved_store_view');
        //Create product and assign to Website2
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($productData);
        $this->assertMessagePresent('success', 'success_saved_product');
        //Generate request path and open it
        $url = str_replace(array('(', ')'), array('-', ''), $productData['autosettings_url_key']) . '.html';
        //Open product URl
        $this->frontend();
        $this->url($url);
        $this->assertSame($this->title(), '404 Not Found 1', 'Wrong page is opened');
    }

    /**
     * <p> Category URl rewrite for two different websites</p>
     *
     * @test
     * @TestlinkId TL-MAGE-5533
     */

    public function forCategoryLinkToOtherWebsite()
    {
        $category = $this->loadDataSet('Category', 'root_category_required');
        $categoryData = $this->loadDataSet('Category', 'sub_category_required_url_rewrite',
            array('parent_category' => $category['name'], 'url_key' => 'testCategory'));
        $websiteDataOne = $this->loadDataSet('Website', 'generic_website');
        $storeData = $this->loadDataSet('Store', 'generic_store',
            array('website' => $websiteDataOne['website_name'], 'root_category' => $category['name']));
        $storeViewData = $this->loadDataSet('StoreView', 'generic_store_view',
            array('store_name' => $storeData['store_name']));
        //Create Root for Website 2
        $this->navigate('manage_categories', false);
        $this->categoryHelper()->checkCategoriesPage();
        $this->categoryHelper()->createCategory($category);
        $this->assertMessagePresent('success', 'success_saved_category');
        //Create SubCategory
        $this->categoryHelper()->createCategory($categoryData);
        $this->assertMessagePresent('success', 'success_saved_category');
        // Crete Website 2 with Store and Store View
        $this->navigate('manage_stores');
        $this->storeHelper()->createStore($websiteDataOne, 'website');
        $this->assertMessagePresent('success', 'success_saved_website');
        $this->storeHelper()->createStore($storeData, 'store');
        $this->assertMessagePresent('success', 'success_saved_store');
        $this->storeHelper()->createStore($storeViewData, 'store_view');
        $this->assertMessagePresent('success', 'success_saved_store_view');
        //Generate request path and open it
        $url = str_replace(array('(', ')'), array('-', ''), $categoryData['url_key']) . '.html';
        //Open product URl
        $this->frontend();
        $this->url($url);
        $this->assertSame($this->title(), '404 Not Found 1', 'Wrong page is opened');
    }

    /**
     * <p>Custom URL Rewrite for product in scope of the same one store<p>
     *
     * @return string
     * @test
     * @TestlinkId TL-MAGE-5565
     */
    public function customProductUrlRewriteSameStore()
    {
        $productData = $this->loadDataSet('Product', 'simple_product_url_rewrite');
        $fieldData = $this->loadDataSet('UrlRewrite', 'url_rewrite_product_custom');
        //Create Simple Product
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($productData);
        $this->assertMessagePresent('success', 'success_saved_product');
        //Open URL rewrite management
        $this->navigate('url_rewrite_management');
        //Click 'Add new rewrite' button
        $this->clickButton('add_new_rewrite');
        //At Create URL rewrite dropdown select For category
        $this->fillDropdown('create_url_rewrite_dropdown', 'Custom');
        $this->waitForPageToLoad();
        $this->validatePage();
        //Generate request path and open it
        $url = str_replace(array('(', ')'), array('-', ''), $productData['autosettings_url_key']) . '.html';
        //Fill fields
        $this->fillField('id_path', $fieldData['id_path']);
        $this->fillField('request_path', $fieldData['request_path']);
        $this->fillField('target_path', $url);
        //        Need to be uncommented when target store functionality will be merged
        //        $this->fillDropdown('target_store', 'Default Store View');
        $this->fillDropdown('request_store', 'Default Store View');
        //Click Save button
        $this->saveForm('save');
        $this->assertMessagePresent('success', 'success_saved_url_rewrite');
        //Generating URL rewrite link
        $rewriteUrl = $this->xmlSitemapHelper()->getFileUrl($url);
        //Open URL rewrite for category on frontend
        $this->frontend();
        $this->url($rewriteUrl);
        //Verifying page of URL rewrite for category
        $this->assertSame($this->title(), $productData['general_name'], 'Wrong page is opened');
        return ($url);
    }

    /**
     * <p>Custom product URL rewrite created for the same one store should not work for other store<p>
     *
     * @param string $url
     *
     * @test
     * @depends customProductUrlRewriteSameStore
     * @TestlinkId TL-MAGE-5571
     */
    public function customProductUrlRewriteOtherStore($url)
    {
        $this->markTestIncomplete('MAGETWO-6995');
        $category = $this->loadDataSet('Category', 'root_category_required');
        $storeData = $this->loadDataSet('Store', 'generic_store', array('root_category' => $category['name']));
        $storeViewData = $this->loadDataSet('StoreView', 'generic_store_view',
            array('store_name' => $storeData['store_name']));
        //Create Category
        $this->navigate('manage_categories', false);
        $this->categoryHelper()->checkCategoriesPage();
        $this->categoryHelper()->createCategory($category);
        $this->assertMessagePresent('success', 'success_saved_category');
        //Create Store and Store View
        $this->navigate('manage_stores');
        $this->storeHelper()->createStore($storeData, 'store');
        $this->assertMessagePresent('success', 'success_saved_store');
        $this->storeHelper()->createStore($storeViewData, 'store_view');
        $this->assertMessagePresent('success', 'success_saved_store_view');
        //Select other store
        $this->frontend();
        $this->addParameter('store', $storeData['store_name']);
        $this->addParameter('storeViewCode', $storeViewData['store_view_code']);
        $this->clickControl('link', 'select_store', false);
        $this->waitForPageToLoad();
        //Generating URL rewrite link
        $rewriteUrl = $this->xmlSitemapHelper()->getFileUrl($url);
        //Open URL rewrite for category on frontend
        $this->frontend();
        $this->url($rewriteUrl);
        //Verifying page of URL rewrite for category
        $this->assertSame($this->title(), '404 Not Found 1', 'Wrong page is opened');
    }

    /**
     * <p>URL Rewrite for product in scope of two different Websites</p>
     *
     * @test
     * @TestlinkId TL-MAGE-5512
     */
    public function productRewriteToOtherStore()
    {
        $this->markTestIncomplete('MAGETWO-6995');
        $category = $this->loadDataSet('Category', 'root_category_required');
        $storeData = $this->loadDataSet('Store', 'generic_store', array('root_category' => $category['name']));
        $storeViewData = $this->loadDataSet('StoreView', 'generic_store_view',
            array('store_name' => $storeData['store_name']));
        $productData = $this->loadDataSet('Product', 'simple_product_url_rewrite',
            array('general_categories' => $category['name']));
        $productSearch = $this->loadDataSet('Product', 'product_search',
            array('product_sku' => $productData['general_sku']));
        //Create Root for Store 2
        $this->navigate('manage_categories', false);
        $this->categoryHelper()->checkCategoriesPage();
        $this->categoryHelper()->createCategory($category);
        $this->assertMessagePresent('success', 'success_saved_category');
        $this->navigate('manage_stores');
        // Crete Store 2 and Store View 2
        $this->storeHelper()->createStore($storeData, 'store');
        $this->assertMessagePresent('success', 'success_saved_store');
        $this->storeHelper()->createStore($storeViewData, 'store_view');
        $this->assertMessagePresent('success', 'success_saved_store_view');
        //Create product and assign to Website2
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($productData);
        $this->assertMessagePresent('success', 'success_saved_product');
        //Open Manage URL rewrite page
        $this->admin('url_rewrite_management');
        //Click 'Add new rewrite' button
        $this->clickButton('add_new_rewrite');
        //Select "For Product"
        $this->fillDropdown('create_url_rewrite_dropdown', 'For product');
        $this->waitForPageToLoad();
        //Find product in the Grid and open it
        $this->validatePage('add_new_urlrewrite_product');
        $this->searchAndOpen($productSearch, 'product_rewrite', false);
        $this->waitForPageToLoad();
        $this->addParameter('id', $this->defineParameterFromUrl('product'));
        $this->validatePage();
        //Select Category
        $this->addParameter('rootName', $productData['general_categories']);
        $this->clickControl('link', 'root_category', false);
        $this->waitForPageToLoad();
        $this->addParameter('categoryId', $this->defineParameterFromUrl('category'));
        $this->validatePage();
        //Check that 'Default Store View' isn\t present in request store
        $options = $this->select($this->getControlElement('dropdown', 'request_store'))->selectOptionLabels();
        $this->assertFalse(in_array('Default Store View', $options),
            'Option with value "Default Store View" is present in "request_store" dropdown');
    }
}
