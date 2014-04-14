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
        $this->navigate('url_rewrite_management');
    }

    /**
     * @test
     */
    public function preconditionsForTests()
    {
        $subCategory = $this->loadDataSet('Category', 'sub_category_required');
        $categoryPath = $subCategory['parent_category'] . '/' . $subCategory['name'];
        $product = $this->loadDataSet('Product', 'simple_product_url_rewrite',
            array('general_categories' => $categoryPath));
        $cmsPage = $this->loadDataSet('CmsPage', 'new_cms_page_req');

        $categoryForStore = $this->loadDataSet('Category', 'root_category_required');
        $otherStore = $this->loadDataSet('Store', 'generic_store', array('root_category' => $categoryForStore['name']));
        $storeViewForOtherStore = $this->loadDataSet('StoreView', 'generic_store_view',
            array('store_name' => $otherStore['store_name']));
        //Steps
        $this->navigate('manage_categories', false);
        $this->categoryHelper()->checkCategoriesPage();
        $this->categoryHelper()->createCategory($subCategory);
        $this->assertMessagePresent('success', 'success_saved_category');
        $this->categoryHelper()->createCategory($categoryForStore);
        $this->assertMessagePresent(self::MESSAGE_TYPE_SUCCESS, 'success_saved_category');

        $this->navigate('manage_products');
        $this->productHelper()->createProduct($product);
        $this->assertMessagePresent(self::MESSAGE_TYPE_SUCCESS, 'success_saved_product');

        $this->navigate('manage_cms_pages');
        $this->cmsPagesHelper()->createCmsPage($cmsPage);
        $this->assertMessagePresent(self::MESSAGE_TYPE_SUCCESS, 'success_saved_cms_page');

        $this->navigate('manage_stores');
        $this->storeHelper()->createStore($otherStore, 'store');
        $this->assertMessagePresent(self::MESSAGE_TYPE_SUCCESS, 'success_saved_store');
        $this->storeHelper()->createStore($storeViewForOtherStore, 'store_view');
        $this->assertMessagePresent(self::MESSAGE_TYPE_SUCCESS, 'success_saved_store_view');

        $this->reindexInvalidedData();
        $this->flushCache();

        return array(
            'productSku' => $product['general_sku'],
            'productName' => $product['general_name'],
            'categoryPath' => $categoryPath,
            'categoryName' => $subCategory['name'],
            'storeName' => $otherStore['store_name'],
            'cmsUrl' => $cmsPage['page_information']['url_key'],
            'productUrlKey' => $product['autosettings_url_key'],
            'cmsName' => $cmsPage['page_information']['page_title'],
            'storeView' => $storeViewForOtherStore['store_view_name']
        );
    }

    /**
     * Verify that url rewrite form is present at the backend page
     *
     * @test
     */
    public function isFormPresent()
    {
        //Steps
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
        $rewrite = $this->loadDataSet('UrlRewrite', 'url_rewrite_custom', array($emptyField => '%noValue%'));
        //Steps
        $this->urlRewriteHelper()->createUrlRewrite($rewrite);
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
     * Verifying Required field for Custom URl rewrite
     *
     * @test
     * @TestlinkId TL-MAGE-5694
     */
    public function notEditableFieldsForCustom()
    {
        $rewrite = $this->loadDataSet('UrlRewrite', 'url_rewrite_custom');
        //Steps
        $this->urlRewriteHelper()->createUrlRewrite($rewrite, false);
        //Verifying
        $this->assertTrue($this->controlIsEditable(self::FIELD_TYPE_INPUT, 'id_path'), 'ID Path field is not editable');
        $this->assertTrue($this->controlIsEditable(self::FIELD_TYPE_INPUT, 'target_path'),
            'Target Path field is not editable');
    }

    /**
     * Verifying Required field for Product URl rewrite
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-5517
     */
    public function notEditableFieldsForProduct($testData)
    {
        //Data
        $rewrite = $this->loadDataSet('UrlRewrite', 'url_rewrite_product', array(
            'filter_product_sku' => $testData['productSku'],
            'category' => $testData['categoryPath']
        ));
        //Steps
        $this->urlRewriteHelper()->createUrlRewrite($rewrite, false);
        //Verifying
        $this->assertFalse($this->controlIsEditable(self::FIELD_TYPE_INPUT, 'id_path'), 'ID Path field is editable');
        $this->assertFalse($this->controlIsEditable(self::FIELD_TYPE_INPUT, 'target_path'),
            'Target Path field is editable');
    }

    /**
     * Verifying Required field for Category URl rewrite
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-5677
     */
    public function notEditableFieldsForCategory($testData)
    {
        //Data
        $rewrite = $this->loadDataSet('UrlRewrite', 'url_rewrite_category',
            array('category' => $testData['categoryPath']));
        //Steps
        $this->urlRewriteHelper()->createUrlRewrite($rewrite, false);
        //Verifying
        $this->assertFalse($this->controlIsEditable(self::FIELD_TYPE_INPUT, 'id_path'), 'ID Path field is editable');
        $this->assertFalse($this->controlIsEditable(self::FIELD_TYPE_INPUT, 'target_path'),
            'Target Path field is editable');
    }

    /**
     * Create URL rewrite for product
     *
     * @param array $testData
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-5503
     */
    public function urlRewriteForProduct($testData)
    {
        //Data
        $rewrite = $this->loadDataSet('UrlRewrite', 'url_rewrite_product', array(
            'filter_product_sku' => $testData['productSku'],
            'category' => $testData['categoryPath']
        ));
        //Create URL rewrite
        $this->urlRewriteHelper()->createUrlRewrite($rewrite);
        $this->assertMessagePresent(self::MESSAGE_TYPE_SUCCESS, 'success_saved_url_rewrite');
        //Open product by url
        $rewriteUrl = $this->xmlSitemapHelper()->getFileUrl($rewrite['rewrite_info']['request_path']);
        $this->url($rewriteUrl);
        //Verifying
        $this->addParameter('productUrl', str_replace('.html', '', $rewrite['rewrite_info']['request_path']));
        $this->addParameter('elementTitle', $testData['productName']);
        $this->validatePage('product_page');

        return $rewrite['rewrite_info']['request_path'];
    }

    /**
     * Create URL rewrite for product with existing Request path
     *
     * @param array $testData
     * @param string $rewritePath
     *
     * @return string
     *
     * @test
     * @depends preconditionsForTests
     * @depends urlRewriteForProduct
     * @TestlinkId TL-MAGE-5514
     */
    public function urlRewriteForProductExistingPath($testData, $rewritePath)
    {
        //Data
        $rewrite = $this->loadDataSet('UrlRewrite', 'url_rewrite_product', array(
            'request_path' => $rewritePath,
            'filter_product_sku' => $testData['productSku'],
            'category' => $testData['categoryPath']
        ));
        //Create URL rewrite
        $this->urlRewriteHelper()->createUrlRewrite($rewrite);
        //Verifying
        $this->assertMessagePresent('error', 'id_and_req_path_exist');

        return $this->getControlAttribute(self::FIELD_TYPE_INPUT, 'target_path', 'selectedValue');
    }

    /**
     * Product URL rewrite created for the one store should not work for other store
     *
     * @param array $testData
     * @param string $rewritePath
     *
     * @test
     * @depends preconditionsForTests
     * @depends urlRewriteForProduct
     * @TestlinkId TL-MAGE-5508
     */
    public function urlRewriteForProductForOneStore($testData, $rewritePath)
    {
        $rewriteUrl = $this->xmlSitemapHelper()->getFileUrl($rewritePath);
        $this->frontend();
        $this->selectFrontendStore($testData['storeName']);
        $this->url($rewriteUrl);
        //Verifying
        $this->assertSame('404 Not Found 1', $this->title(), 'Wrong page is opened');
    }

    /**
     * Category URL rewrite for the same store
     *
     * @param array $testData
     *
     * @return string
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-5515
     */
    public function urlRewriteForCategory($testData)
    {
        //Data
        $rewrite = $this->loadDataSet('UrlRewrite', 'url_rewrite_category',
            array('category' => $testData['categoryPath']));
        //Create URL rewrite
        $this->urlRewriteHelper()->createUrlRewrite($rewrite);
        $this->assertMessagePresent(self::MESSAGE_TYPE_SUCCESS, 'success_saved_url_rewrite');
        //Open category by url
        $rewriteUrl = $this->xmlSitemapHelper()->getFileUrl($rewrite['rewrite_info']['request_path']);
        $this->url($rewriteUrl);
        //Verifying
        $this->addParameter('categoryUrl', str_replace('.html', '', $rewrite['rewrite_info']['request_path']));
        $this->addParameter('elementTitle', $testData['categoryName']);
        $this->validatePage('category_page_before_reindex');

        return $rewriteUrl;
    }

    /**
     * URL Rewrite for category not available from other store
     *
     * @param array $testData
     * @param string $rewriteUrl
     *
     * @test
     * @depends preconditionsForTests
     * @depends urlRewriteForCategory
     * @TestlinkId TL-MAGE-5516
     */
    public function urlRewriteForCategoryForOneStore($testData, $rewriteUrl)
    {
        $this->frontend();
        $this->selectFrontendStore($testData['storeName']);
        $this->url($rewriteUrl);
        $this->assertSame('404 Not Found 1', $this->title(), 'Wrong page is opened');
    }

    /**
     * Custom URl rewrite for CMS Page
     *
     * @param array $testData
     *
     * @test
     * @depends preconditionsForTests
     * @test
     * @TestlinkId TL-MAGE-6049
     */
    public function customUrlRewriteForCmsPage($testData)
    {
        $this->markTestIncomplete('BUG: CMS Page Link widget is not displayed on frontend');
        //Data
        $rewrite = $this->loadDataSet('UrlRewrite', 'url_rewrite_custom', array(
            'target_path' => $testData['cmsUrl'],
        ));
        //Steps
        $this->urlRewriteHelper()->createUrlRewrite($rewrite);
        $this->assertMessagePresent(self::MESSAGE_TYPE_SUCCESS, 'success_saved_url_rewrite');
        //Open cms page by url
        $rewriteUrl = $this->xmlSitemapHelper()->getFileUrl($rewrite['rewrite_info']['request_path']);
        $this->url($rewriteUrl);
        //Verifying
        $this->addParameter('url_key', $rewrite['rewrite_info']['request_path']);
        $this->addParameter('elementTitle', $testData['cmsName']);
        $this->validatePage('test_page');
        $this->assertTrue($this->controlIsVisible('pageelement', 'widget_cms_link'));
    }

    /**
     * Custom URL Rewrites using an external link
     *
     * @test
     * @TestlinkId TL-MAGE-5507
     */
    public function customUrlRewriteToExternalLink()
    {
        //Data
        $rewrite = $this->loadDataSet('UrlRewrite', 'url_rewrite_custom', array(
            'target_path' => 'http://google.com',
        ));
        //Steps
        $this->urlRewriteHelper()->createUrlRewrite($rewrite);
        $this->assertMessagePresent(self::MESSAGE_TYPE_SUCCESS, 'success_saved_url_rewrite');;
        //Verifying
        $rewriteUrl = $this->xmlSitemapHelper()->getFileUrl($rewrite['rewrite_info']['request_path']);
        $this->url($rewriteUrl);
        //Verifying
        $this->assertSame('Google', $this->title(), 'Wrong page is opened');
    }

    /**
     * URL Rewrite from CMS Page to the external link
     *
     * @param array $testData
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-6123
     */
    public function cmsUrlRewriteToExternalLink($testData)
    {
        //Data
        $rewrite = $this->loadDataSet('UrlRewrite', 'url_rewrite_custom', array(
            'request_path' => $testData['cmsUrl'],
            'target_path' => 'http://google.com',
        ));
        //Steps
        $this->urlRewriteHelper()->createUrlRewrite($rewrite);
        $this->assertMessagePresent(self::MESSAGE_TYPE_SUCCESS, 'success_saved_url_rewrite');;
        //Verifying
        $rewriteUrl = $this->xmlSitemapHelper()->getFileUrl($rewrite['rewrite_info']['request_path']);
        $this->url($rewriteUrl);
        //Verifying
        $this->assertSame('Google', $this->title(), 'Wrong page is opened');
    }

    /**
     * Custom URL Rewrite for product in scope of the same one store
     *
     * @param array $testData
     * @param string $rewritePath
     *
     * @test
     * @depends preconditionsForTests
     * @depends urlRewriteForProductExistingPath
     * @TestlinkId TL-MAGE-5565
     */
    public function customUrlRewriteForProduct($testData, $rewritePath)
    {
        //Data
        $rewrite = $this->loadDataSet('UrlRewrite', 'url_rewrite_custom', array(
            'target_path' => $rewritePath,
        ));
        //Steps
        $this->urlRewriteHelper()->createUrlRewrite($rewrite);
        $this->assertMessagePresent(self::MESSAGE_TYPE_SUCCESS, 'success_saved_url_rewrite');
        //Open cms page by url
        $rewriteUrl = $this->xmlSitemapHelper()->getFileUrl($rewrite['rewrite_info']['request_path']);
        $this->url($rewriteUrl);
        //Verifying
        $this->addParameter('productUrl', str_replace('.html', '', $rewrite['rewrite_info']['request_path']));
        $this->addParameter('elementTitle', $testData['productName']);
        $this->validatePage('product_page');
    }

    /**
     * URL Rewrite for product in scope of two different stores (negative)
     *
     * @param array $testData
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-5512
     */
    public function urlRewriteForProductWithCategoryForOneStore($testData)
    {
        //Data
        $rewrite = $this->loadDataSet('UrlRewrite', 'url_rewrite_product', array(
            'filter_product_sku' => $testData['productSku'],
            'category' => $testData['categoryPath']
        ));
        //Create URL rewrite
        $this->urlRewriteHelper()->createUrlRewrite($rewrite, false);
        //Check that 'Default Store View' isn\t present in request store
        $options = $this->select($this->getControlElement(self::FIELD_TYPE_DROPDOWN, 'store'))->selectOptionLabels();
        $options = array_map('trim', $options);
        $this->assertFalse(in_array($testData['storeView'], $options),
            'Option with value "' . $testData['storeView'] . '" is present in "store" dropdown');
    }
}