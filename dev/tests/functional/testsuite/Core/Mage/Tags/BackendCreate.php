<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Tags
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Tag creation tests for Backend
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_Tags_BackendCreateTest extends Mage_Selenium_TestCase
{
    /**
     * <p>Preconditions:</p>
     * <p>Navigate to Catalog -> Tags -> All tags</p>
     */
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->navigate('all_tags');
    }

    protected function tearDownAfterTest()
    {
        $this->loginAdminUser();
        $this->navigate('all_tags');
        $this->tagsHelper()->deleteAllTags();
    }

    /**
     * <p>Creating a new tag</p>
     *
     * @test
     * @TestlinkId TL-MAGE-3494, TL-MAGE-2348
     */
    public function createNew()
    {
        //Setup
        $setData = $this->loadDataSet('Tag', 'backend_new_tag');
        //Steps
        $this->tagsHelper()->addTag($setData);
        //Verify
        $this->assertTrue($this->checkCurrentPage('all_tags'), $this->getParsedMessages());
        $this->assertMessagePresent('success', 'success_saved_tag');
        $this->assertNotNull($this->search(array('tag_name'   => $setData['tag_name'],
                                                 'tag_status' => $setData['tag_status']), 'tags_grid'));
    }

    /**
     * <p>Creating a new tag in backend with empty required fields.</p>
     *
     * @test
     * @TestlinkId TL-MAGE-3496
     */
    public function withEmptyTagName()
    {
        //Setup
        $setData = $this->loadDataSet('Tag', 'backend_new_tag', array('tag_name' => ''));
        //Steps
        $this->tagsHelper()->addTag($setData);
        //Verify
        $this->addFieldIdToMessage('field', 'tag_name');
        $this->assertMessagePresent('validation', 'empty_required_field');
        $this->assertTrue($this->verifyMessagesCount(), $this->getParsedMessages());
    }

    /**
     * <p>Creating a new tag with special values (long, special chars).</p>
     *
     * @param array $specialValue
     *
     * @test
     * @dataProvider withSpecialValuesDataProvider
     * @depends createNew
     * @TestlinkId TL-MAGE-3497
     */
    public function withSpecialValues(array $specialValue)
    {
        //Data
        $setData = $this->loadDataSet('Tag', 'backend_new_tag', $specialValue);
        $tagToOpen = $this->loadDataSet('Tag', 'backend_search_tag', array('tag_name' => $setData['tag_name']));
        //Steps
        $this->tagsHelper()->addTag($setData);
        //Verify
        $this->assertTrue($this->checkCurrentPage('all_tags'), $this->getParsedMessages());
        $this->assertMessagePresent('success', 'success_saved_tag');
        $this->tagsHelper()->openTag($tagToOpen);
        $this->verifyForm($setData);
    }

    public function withSpecialValuesDataProvider()
    {
        return array(
            array(array('tag_name' => $this->generate('string', 255))), //long
            array(array('tag_name' => $this->generate('string', 50, ':punct:'))), //special chars
            array(array('base_popularity' => '4294967295')), //max int(10) unsigned
        );
    }

    /**
     * <p>Creating a tag and assign it to a product as administrator</p>
     *
     * @test
     * @TestlinkId TL-MAGE-3495
     */
    public function productTaggedByAdministrator()
    {
        $simple = $this->loadDataSet('Product', 'simple_product_visible');
        $setData = $this->loadDataSet('Tag', 'backend_new_tag_with_product',
            array('prod_tag_admin_name' => $simple['general_name']));
        //Setup
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($simple);
        $this->assertMessagePresent('success', 'success_saved_product');
        //Steps
        $this->navigate('all_tags');
        $this->tagsHelper()->addTag($setData);
        //Verify
        $this->assertTrue($this->checkCurrentPage('all_tags'), $this->getParsedMessages());
        $this->assertMessagePresent('success', 'success_saved_tag');
        $tagSearchData = array('tag_name' => $setData['tag_name']);
        $productSearchData = array('general_name' => $simple['general_name']);
        $this->navigate('manage_products');
        $this->assertTrue($this->tagsHelper()->verifyTagProduct($tagSearchData, $productSearchData),
            $this->getParsedMessages());
    }
}