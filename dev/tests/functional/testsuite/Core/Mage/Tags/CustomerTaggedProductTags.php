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
class Core_Mage_Tags_CustomerTaggedProductTagsTest extends Core_Mage_Tags_TagsFixtureAbstract
{
    /**
     * @return array
     * @test
     * @skipTearDown
     */
    public function preconditionsForTests()
    {
        return parent::_preconditionsForTaggedProductTests();
    }

    /**
     * Backend verification customer tagged product from frontend on the Product Page
     * Verify starting to edit customer
     *
     * @param string $tags
     * @param string $status
     * @param integer $customer
     * @param array $testData
     *
     * @test
     * @dataProvider tagNameDataProvider
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-6040, TL-MAGE-6043
     */
    public function addFromFrontendTags($tags, $status, $customer, $testData)
    {
        $this->markTestIncomplete('BUG: wrong url for customer on Customers Tagged Product tab');
        //Setup
        $this->customerHelper()->frontLoginCustomer($testData['user'][$customer]);
        $this->productHelper()->frontOpenProduct($testData['simple']);
        //Steps
        $this->tagsHelper()->frontendAddTag($tags);
        //Verification
        $this->assertMessagePresent('success', 'tag_accepted_success');
        $this->tagsHelper()->frontendTagVerification($tags, $testData['simple']);
        $tags = $this->tagsHelper()->_convertTagsStringToArray($tags);
        //Open tagged product
        $this->loginAdminUser();
        foreach ($tags as $tag) {
            $searchTag = array('tag_search_name' => $tag, 'tag_search_email' => $testData['user'][$customer]['email']);
            $searchProduct = array('product_name' => $testData['simple']);
            if ($status != 'Pending') {
                $this->navigate('all_tags');
                $this->tagsHelper()->changeTagsStatus(array(array('tag_name' => $tag)), $status);
            }
            $this->navigate('manage_products');
            $this->assertTrue($this->tagsHelper()->verifyCustomerTaggedProduct($searchTag, $searchProduct),
                'Customer tagged product verification is failure');
            $this->customerHelper()->openCustomer(array(
                'tag_search_email' => $testData['user'][$customer]['email'],
                'tag_search_name' => $tag
            ), 'customer_tags');
            $this->saveForm('save_customer');
            $this->assertMessagePresent('success', 'success_saved_customer');
        }
    }

    public function tagNameDataProvider()
    {
        return array(
            array($this->generate('string', 4, ':alpha:'), 'Approved', 1),
            array($this->generate('string', 4, ':alpha:'), 'Disabled', 2)
        );
    }

    /**
     * Backend verification customer tagged product from backend on the Product Page
     *
     * @param string $tags
     * @param string $status
     * @param array $testData
     *
     * @test
     * @dataProvider tagAdminNameDataProvider
     * @depends preconditionsForTests
     * TestlinkId TL-MAGE-6041
     */
    public function addFromBackendTags($tags, $status, $testData)
    {
        $setData = $this->loadDataSet('Tag', 'backend_new_tag_with_product', array(
            'tag_name' => $tags,
            'tag_status' => 'Pending',
            'prod_tag_admin_name' => $testData['simple'],
            'base_popularity' => '0',
            'choose_store_view' => '%noValue%'
        ));
        //Setup
        $this->navigate('all_tags');
        $this->tagsHelper()->addTag($setData);
        $tags = $this->tagsHelper()->_convertTagsStringToArray($tags);
        //Open tagged product
        foreach ($tags as $tag) {
            $searchTag = array('tag_customer_search_name' => $tag);
            $searchProduct = array('product_name' => $testData['simple']);
            $this->navigate('all_tags');
            $this->tagsHelper()->changeTagsStatus(array($searchTag), $status);
            $this->navigate('manage_products');
            $this->assertFalse($this->tagsHelper()->verifyCustomerTaggedProduct($searchTag, $searchProduct),
                'Administrator tagged product verification is failure');
        }
    }

    public function tagAdminNameDataProvider()
    {
        return array(
            array($this->generate('string', 4, ':alpha:'), 'Approved'),
            array($this->generate('string', 4, ':alpha:'), 'Disabled')
        );
    }

    /**
     * Backend verification customer tagged product searching
     *
     * @param string $tags
     * @param array $testData
     *
     * @test
     * @dataProvider tagSearchNameDataProvider
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-6042
     */
    public function searchTags($tags, $testData)
    {
        $searchTagProduct = array(
            'tag_search_name' => $tags['tag_name'],
            'tag_search_email' => $testData['user'][1]['email']
        );
        $searchTagCustomer = array(
            'tag_customer_search_name' => $tags['tag_name'],
            'tag_customer_search_email' => $testData['user'][1]['email']
        );
        //Steps
        $this->customerHelper()->frontLoginCustomer($testData['user'][1]);
        $this->productHelper()->frontOpenProduct($testData['simple']);
        $this->tagsHelper()->frontendAddTag($tags['tag_name']);
        //Verification
        $this->assertMessagePresent('success', 'tag_accepted_success');
        //Steps
        $this->loginAdminUser();
        $this->navigate('all_tags');
        //Change statuses product tags
        $this->tagsHelper()->changeTagsStatus(array(array('tag_name' => $tags['tag_name'])), $tags['tag_status']);
        $this->assertMessagePresent('success');
        $this->navigate('manage_products');
        $this->assertTrue(
            $this->tagsHelper()->verifyCustomerTaggedProduct($searchTagProduct,
                array('product_name' => $testData['simple'])),
            'Product verification is failure'
        );
        //Fill filter
        $this->fillForm($searchTagCustomer);
        $this->clickButton('search', false);
        $this->waitForAjax();
        //Check records count
        $totalCount = $this->getTotalRecordsInTable('fieldset', 'customer_tags');
        $this->assertEquals(1, $totalCount, 'Total records found is incorrect');
        $this->assertNotNull($this->search($searchTagProduct, 'customer_tags'),
            'Tag ' . $tags['tag_name'] . ' is not found');
    }

    public function tagSearchNameDataProvider()
    {
        return array(
            array(array(
                'tag_name' => $this->generate('string', 4, ':alpha:'),
                'tag_status' => 'Approved',
                'base_popularity' => '1'
            )),
            array(array(
                'tag_name' => $this->generate('string', 4, ':alpha:'),
                'tag_status' => 'Pending',
                'base_popularity' => '1'
            )),
            array(array(
                'tag_name' => $this->generate('string', 4, ':alpha:'),
                'tag_status' => 'Disabled',
                'base_popularity' => '1'
            ))
        );
    }
}