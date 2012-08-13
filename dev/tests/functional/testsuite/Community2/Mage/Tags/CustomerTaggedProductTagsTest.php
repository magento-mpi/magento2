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
class Community2_Mage_Tags_CustomerTaggedProductCreateTest extends Mage_Selenium_TestCase
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

    protected function tearDownAfterTestClass()
    {
        $this->loginAdminUser();
        $this->navigate('all_tags');
        $this->tagsHelper()->deleteAllTags();
    }

    /**
     * @return array
     * @test
     * @skipTearDown
     */
    public function preconditionsForTests()
    {
        //Data
        $userData = array();
        $userData[1] = $this->loadDataSet('Customers', 'generic_customer_account');
        $userData[2] = $this->loadDataSet('Customers', 'generic_customer_account');
        //Steps and Verification
        $this->navigate('manage_customers');
        $this->customerHelper()->createCustomer($userData[1]);
        $this->assertMessagePresent('success', 'success_saved_customer');
        $this->customerHelper()->createCustomer($userData[2]);
        $this->assertMessagePresent('success', 'success_saved_customer');
        $simple = $this->productHelper()->createSimpleProduct(true);
        $this->reindexInvalidedData();
        $this->flushCache();
        $userData[1] = array('email' => $userData[1]['email'], 'password' => $userData[1]['password']);
        $userData[2] = array('email' => $userData[2]['email'], 'password' => $userData[2]['password']);
        return array('user'     => $userData,
            'simple'   => $simple['simple']['product_name'],
            'category' => $simple['category']['path']);
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
        //Setup
        $this->customerHelper()->frontLoginCustomer($testData['user'][$customer]);
        $this->productHelper()->frontOpenProduct($testData['simple']);
        //Steps
        $this->tagsHelper()->frontendAddTag($tags);
        //Verification
        $this->assertMessagePresent('success', 'tag_accepted_success');
        $this->tagsHelper()->frontendTagVerification($tags, $testData['simple']);
        $tags = $this->tagsHelper()->convertTagsStringToArray($tags);
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
            $this->assertTrue($this->tagsHelper()->verifyCustomerTaggedProduct(
                array(
                    'tag_search_name' => $tag,
                    'tag_search_email' => $testData['user'][$customer]['email']),
                array('product_name' => $testData['simple'])),
            'Customer tagged product verification is failure');
            $this->addParameter('customer_first_last_name',
                'First Name Last Name');
            $this->searchAndOpen(
                array(
                    'tag_search_email' => $testData['user'][$customer]['email'],
                    'tag_search_name' => $tag), true, 'customer_tags'
            );
            $this->customerHelper()->saveForm('save_customer');
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
        $tags = $this->tagsHelper()->convertTagsStringToArray($tags);
        //Open tagged product
        foreach ($tags as $tag) {
            $this->navigate('manage_products');
            $this->assertFalse($this->tagsHelper()->verifyCustomerTaggedProduct(
                    array('tag_search_name' => $tag),
                    array('product_name' => $testData['simple'])),
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
        $this->assertTrue($this->tagsHelper()->verifyCustomerTaggedProduct(
                array(
                    'tag_search_name' => $tags,
                    'tag_search_email' => $testData['user'][1]['email']),
                array('product_name' => $testData['simple'])),
            'Product verification is failure');
        //Fill filter
        $this->tagsHelper()->fillForm(
            array(
                'tag_customer_search_name' => $tags['tag_name'],
                'tag_customer_search_email' => $testData['user'][1]['email']
            ));
        $this->clickButton('search', false);
        $this->waitForAjax();
        //Check records count
        $totalCount = intval($this->getText($this->_getControlXpath('pageelement', 'qtyElementsInTable')));
        $this->assertEquals(1, $totalCount,
            'Total records found is incorrect');
        $this->assertTrue((bool) $this->tagsHelper()->search(
                array(
                    'tag_search_name' => $tags['tag_name'],
                    'tag_search_email' => $testData['user'][1]['email']
                ), null, false),
            'Tags search is failed: ' . print_r($tags['tag_name'], true));
    }
    public function tagSearchNameDataProvider()
    {
        return array(
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