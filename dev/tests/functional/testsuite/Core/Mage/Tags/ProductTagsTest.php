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
require_once 'TagsFixtureAbstract.php';
/**
 * Tag creation tests for Backend
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_Tags_ProductTagsTest extends Core_Mage_Tags_TagsFixtureAbstract
{
    /**
     * @return array
     * @test
     * @skipTearDown
     */
    public function preconditionsForTests()
    {
        return parent::_preconditionsForAllTagsTests();
    }
    /**
     * Backend verification added tag from frontend on the Product Page
     *
     * @param string $tags
     * @param string $status
     * @param array $testData
     *
     * @test
     * @dataProvider tagNameDataProvider
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-2369
     */
    public function addFromFrontendTags($tags, $status, $testData)
    {
        $this->markTestSkipped('Due to bug MAGETWO-8429');
        //Setup
        $this->customerHelper()->frontLoginCustomer($testData['user'][1]);
        $this->productHelper()->frontOpenProduct($testData['simple']);
        //Steps
        $this->tagsHelper()->frontendAddTag($tags);
        //Verification
        $this->assertMessagePresent('success', 'tag_accepted_success');
        $this->tagsHelper()->frontendTagVerification($tags, $testData['simple']);
        $tags = $this->tagsHelper()->_convertTagsStringToArray($tags);
        $this->loginAdminUser();
        if ($status != 'Pending') {
            $this->navigate('all_tags');
            foreach ($tags as $tag) {
                $this->tagsHelper()->changeTagsStatus(array(array('tag_name' => $tag)), $status);
            }
        }
        //Open tagged product
        foreach ($tags as $tag) {
            $this->navigate('manage_products');
            $this->assertTrue($this->tagsHelper()->verifyTagProduct(
                array(
                    'tag_name' => $tag,
                    'status' => $status,
                    'tag_search_num_of_use_from' => '1',
                    'tag_search_num_of_use_to' => '1'),
                array('product_name' => $testData['simple'])),
            'Product tags verification is failure');
        }
    }
    /**
     * Backend verification added tag from backend on the Product Page
     * Start editing tag from Product Tags
     *
     * @param string $tags
     * @param string $status
     * @param array $testData
     *
     * @test
     * @dataProvider tagNameDataProvider
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-2371, TL-MAGE-2374
     */
    public function addFromBackendTags($tags, $status, $testData)
    {
        $this->markTestSkipped('Due to bug MAGETWO-8429');
        $setData = $this->loadDataSet('Tag', 'backend_new_tag_with_product',
            array(
                'tag_name' => $tags,
                'tag_status' => 'Pending',
                'prod_tag_admin_name' => $testData['simple'],
                'base_popularity' => '0',
                'switch_store' => '%noValue%'));
        //Setup
        $this->navigate('all_tags');
        $this->tagsHelper()->addTag($setData);
        $this->navigate('pending_tags');
        $this->tagsHelper()->changeTagsStatus(array(array('tag_name' => $tags)), $status);
        //Steps
        $tags = $this->tagsHelper()->_convertTagsStringToArray($tags);
        //Open tagged product
        foreach ($tags as $tag) {
            $this->navigate('manage_products');
            $this->assertTrue($this->tagsHelper()->verifyTagProduct(
                array(
                    'tag_name' => $tag,
                    'status' => 'Disabled',
                    'tag_search_num_of_use_from' => '0',
                    'tag_search_num_of_use_to' => '0'),
                array('product_name' => $testData['simple'])),
            'Product tags verification is failure');
            //Open tag
            $this->tagsHelper()->openTag(
                array(
                    'tag_name' => $tag,
                    'status' => 'Disabled')
                );
            //Verify tag
            $this->assertTrue($this->verifyForm(
                array(
                    'tag_name' => $tags,
                    'tag_status' => 'Disabled',
                    'base_popularity' => '0')),
                'Tag verification is failure ' . print_r($tags, true));
            $this->saveForm('save_tag');
            $this->assertMessagePresent('success', 'success_saved_tag');
        }
    }
    public function tagNameDataProvider()
    {
        return array(
            //TL-MAGE-2371, TL-MAGE-2374 simple tag
            array($this->generate('string', 4, ':alpha:'), 'Disabled'),
        );
    }
    /**
     * Backend verification added tag from backend on the Product Page
     * Start editing tag from Product Tags
     *
     * @param string $tags
     * @param array $testData
     *
     * @test
     * @dataProvider tagSearchNameDataProvider
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-2373
     */
    public function searchTags($tags, $testData)
    {
        $this->markTestSkipped('Due to bug MAGETWO-8429');
        $this->customerHelper()->frontLoginCustomer($testData['user'][1]);
        $this->productHelper()->frontOpenProduct($testData['simple']);
        //Steps
        $this->tagsHelper()->frontendAddTag($tags['tag_name']);
        //Verification
        $this->assertMessagePresent('success', 'tag_accepted_success');
        $this->loginAdminUser();
        $this->navigate('pending_tags');
        //Change statuses product tags
        $this->tagsHelper()->changeTagsStatus(array(array('tag_name' => $tags['tag_name'])), $tags['tag_status']);
        $this->navigate('manage_products');
        $this->assertTrue($this->tagsHelper()->verifyTagProduct(
            array(
                'tag_name' => $tags['tag_name'],
                'status' => $tags['tag_status'],
                'tag_search_num_of_use_from' => $tags['base_popularity'],
                'tag_search_num_of_use_to' => $tags['base_popularity']),
            array('product_name' => $testData['simple'])),
        'Product verification is failure');
        //Fill filter
        $this->tagsHelper()->fillForm(
            array(
                'tag_search_name' => $tags['tag_name'],
                'tag_search_status' => $tags['tag_status'],
                'tag_search_num_of_use_from' => $tags['base_popularity'],
                'tag_search_num_of_use_to' => $tags['base_popularity']));
        $this->clickButton('search', false);
        $this->waitForAjax();
        //Check records count
        $totalCount = intval($this->getControlAttribute('pageelement', 'qtyElementsInTable', 'text'));
        $this->assertEquals(1, $totalCount, 'Total records found is incorrect');
        $this->assertNotNull($this->tagsHelper()->search(
            array(
                'tag_name'                   => $tags['tag_name'],
                'status'                     => $tags['tag_status'],
                'tag_search_num_of_use_from' => $tags['base_popularity'],
                'tag_search_num_of_use_to'   => $tags['base_popularity']), 'tags_grid'),
        'Tags search is failed: ' . print_r($tags['tag_name'], true));
    }
    public function tagSearchNameDataProvider()
    {
        return array(
            //TL-MAGE-2373 simple tag
            array(
                array('tag_name' => $this->generate('string', 4, ':alpha:'),
                    'tag_status' => 'Approved', 'base_popularity' => '1')),
            array(
                array('tag_name' => $this->generate('string', 4, ':alpha:'),
                    'tag_status' => 'Pending', 'base_popularity' => '1')),
            array(
                array('tag_name' => $this->generate('string', 4, ':alpha:'),
                    'tag_status' => 'Disabled', 'base_popularity' => '1'))
        );
    }

}