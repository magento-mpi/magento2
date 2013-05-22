<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_CmsPages
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Create Cms Page Test
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_CmsPages_CreateTest extends Mage_Selenium_TestCase
{
    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
        $this->navigate('manage_stores');
        $this->storeHelper()->createStore('StoreView/generic_store_view', 'store_view');
        $this->assertMessagePresent('success', 'success_saved_store_view');
    }

    protected function assertPreconditions()
    {
        $this->loginAdminUser();
    }

    /**
     * <p>Preconditions</p>
     * <p>Creates Category to use during tests</p>
     *
     * @return array
     * @test
     */
    public function preconditionsForTests()
    {
        //Data
        $category = $this->loadDataSet('Category', 'sub_category_required');
        $product = $this->loadDataSet(
            'Product',
            'simple_product_visible',
            array('general_categories' => $category['parent_category'] . '/' . $category['name'])
        );
        //Steps
        $this->navigate('manage_categories', false);
        $this->categoryHelper()->checkCategoriesPage();
        $this->categoryHelper()->createCategory($category);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_category');
        //Steps
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($product);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');

        return array('category_path' => $product['general_categories'],
                     'filter_sku'    => $product['general_sku'],);
    }

    /**
     * <p>Creates Page with required fields</p>
     *
     * @return array
     * @test
     * @TestlinkId TL-MAGE-3213
     */
    public function withRequiredFields()
    {
        $this->markTestIncomplete('MAGETWO-8415');
        //Data
        $pageData = $this->loadDataSet('CmsPage', 'new_cms_page_req');
        //Steps
        $this->navigate('manage_cms_pages');
        $this->cmsPagesHelper()->createCmsPage($pageData);
        //Verification
        $this->assertMessagePresent('success', 'success_saved_cms_page');
        $this->cmsPagesHelper()->frontValidatePage($pageData);

        return $pageData;
    }

    /**
     * <p>Creates Page with all fields and all types of widgets</p>
     *
     * @param array $data
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-3210
     */
    public function withAllFields($data)
    {
        $this->markTestIncomplete('MAGETWO-8415');
        //Data
        $pageData = $this->loadDataSet('CmsPage', 'new_page_all_fields', $data);
        //Steps
        $this->navigate('manage_cms_pages');
        $this->cmsPagesHelper()->createCmsPage($pageData);
        //Verification
        $this->assertMessagePresent('success', 'success_saved_cms_page');
        $this->cmsPagesHelper()->frontValidatePage($pageData);
    }

    /**
     * <p>Creates Page with all fields filled except one empty</p>
     *
     * @param string $fieldName
     * @param string $fieldType
     *
     * @test
     * @dataProvider withEmptyRequiredFieldsDataProvider
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-3211
     */
    public function withEmptyRequiredFields($fieldName, $fieldType)
    {
        $this->markTestIncomplete('BUG: Backend validation after js');
        //Data
        $pageData = $this->loadDataSet('CmsPage', 'new_cms_page_req', array($fieldName => '%noValue%'));
        if ($fieldName == 'widget_type') {
            $this->overrideDataByCondition(
                'widget_1', array($fieldName => '-- Please Select --'), $pageData, 'byFieldKey'
            );
        }
        //Steps
        $this->navigate('manage_cms_pages');
        $this->cmsPagesHelper()->createCmsPage($pageData);
        //Verification
        if ($fieldName == 'content') {
            $fieldName = 'editor_disabled';
        }
        $this->addFieldIdToMessage($fieldType, $fieldName);
        $this->assertMessagePresent('validation', 'empty_required_field');
        $this->assertTrue($this->verifyMessagesCount(), $this->getParsedMessages());
    }

    public function withEmptyRequiredFieldsDataProvider()
    {
        return array(
            array('page_title', 'field'),
            array('url_key', 'field'),
            array('content', 'field'),
            array('store_view', 'multiselect'),
            array('widget_type', 'dropdown')
        );
    }

    /**
     * <p>Creates Pages with same URL Key</p>
     *
     * @param array $pageData
     *
     * @test
     * @depends withRequiredFields
     * @TestlinkId TL-MAGE-3212
     */
    public function withExistUrlKey($pageData)
    {
        //Steps
        $this->navigate('manage_cms_pages');
        $this->cmsPagesHelper()->createCmsPage($pageData);
        //Verification
        $this->assertMessagePresent('error', 'existing_url_key');
    }

    /**
     * <p>Creates Pages with numbers in URL Key</p>
     *
     * @param string $urlValue
     * @param string $messageType
     *
     * @test
     * @dataProvider withWrongUrlKeyDataProvider
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-3214
     */
    public function withWrongUrlKey($urlValue, $messageType)
    {
        if ($messageType == 'validation') {
            $this->markTestIncomplete('BUG: Backend validation after js');
        }
        //Data
        $pageData = $this->loadDataSet('CmsPage', 'new_cms_page_req', array('url_key' => $urlValue));
        //Steps
        $this->navigate('manage_cms_pages');
        $this->cmsPagesHelper()->createCmsPage($pageData);
        //Verification
        if ($messageType == 'error') {
            $this->assertMessagePresent('error', 'invalid_url_key_with_numbers_only');
        } else {
            $this->addFieldIdToMessage('field', 'url_key');
            $this->assertMessagePresent('validation', 'invalid_urk_key_spec_sym');
        }
    }

    public function withWrongUrlKeyDataProvider()
    {
        return array(
            array($this->generate('string', 10, ':digit:'), 'error'),
            array($this->generate('string', 10, ':punct:'), 'validation')
        );
    }

    /**
     * <p>Create CMS Page with special values in required fields</p>
     *
     * @param array $fieldData
     *
     * @test
     * @dataProvider withSpecialValueInFieldsDataProvider
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-5298
     */
    public function withSpecialValueInFields($fieldData)
    {
        //Data
        $pageData = $this->loadDataSet('CmsPage', 'new_cms_page_req', $fieldData);
        $search = $this->loadDataSet(
            'CmsPage',
            'search_cms_page',
            array('filter_url_key' => $pageData['page_information']['url_key'])
        );
        //Steps
        $this->navigate('manage_cms_pages');
        $this->cmsPagesHelper()->createCmsPage($pageData);
        //Verification
        $this->assertMessagePresent('success', 'success_saved_cms_page');
        //Steps
        $this->cmsPagesHelper()->openCmsPage($search);
        //Verification
        $this->assertTrue($this->verifyForm($pageData), $this->getParsedMessages());
    }

    public function withSpecialValueInFieldsDataProvider()
    {
        return array(
            array(array('page_title' => $this->generate('string', 255, ':lower:'))),
            array(array('url_key' => $this->generate('string', 100, ':lower:'))),
            array(array('page_title' => $this->generate('string', 64, ':punct:')))
        );
    }
}
