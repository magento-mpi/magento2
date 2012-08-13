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
class Community2_Mage_Tags_CustomerCreateTest extends Mage_Selenium_TestCase
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
       // $this->tagsHelper()->deleteAllTags();
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
        //Steps and Verification
        $this->navigate('manage_customers');
        $this->customerHelper()->createCustomer($userData[1]);
        $this->assertMessagePresent('success', 'success_saved_customer');
        $simple = $this->productHelper()->createSimpleProduct(true);
        $this->reindexInvalidedData();
        $this->flushCache();
        $userData[1] = array('email' => $userData[1]['email'], 'password' => $userData[1]['password']);
        return array('user'     => $userData,
            'simple'   => $simple['simple']['product_name'],
            'category' => $simple['category']['path']);
    }

    /**
     * Backend verification added tag from frontend on the Customer Page
     *
     * @param string $tags
     * @param string $status
     * @param array $testData
     *
     * @test
     * @dataProvider tagNameDataProvider
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-2378, TL-MAGE-2379
     */
    public function addNewTags($tags, $status, $testData)
    {
        //Setup
        $this->customerHelper()->frontLoginCustomer($testData['user'][1]);
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
        //Open customer
        foreach ($tags as $tag) {
            $this->navigate('manage_customers');
            $this->addParameter('customer_first_last_name',
                'First Name Last Name');
            $this->assertTrue($this->tagsHelper()->verifyTagCustomer(
                array(
                    'tag_name' => $tag,
                    'status' => $status,
                    'product_name' => $testData['simple']),
                array('customer_email' => $testData['user'][1]['email'])),
            'Product tags verification is failure');
            $this->tagsHelper()->openTag(
                array(
                    'product_name' => $testData['simple'],
                    'status' => $status,
                    'tag_search_name' => $tag), false
            );
            $this->tagsHelper()->saveForm('save_tag');
            $this->assertMessagePresent('success', 'success_saved_tag');
        }
    }

    public function tagNameDataProvider()
    {
        return array(
            array($this->generate('string', 4, ':alpha:'), 'Disabled'),
            array($this->generate('string', 4, ':alpha:'), 'Approved'),
            array($this->generate('string', 4, ':alpha:'), 'Pending')
        );
    }
    /**
     * Backend verification search and order tag from frontend on the Customer Page
     *
     * @param string $columnName
     * @param array $testData
     *
     * @test
     * @dataProvider tagSearchNameDataProvider
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-2378, TL-MAGE-2379
     */
    public function searchTags($columnName, $testData)
    {
        //Setup
        $this->loginAdminUser();
        $this->navigate('manage_customers');
        $this->customerHelper()->openCustomer(array('customer_email' => $testData['user'][1]['email']));
        $this->openTab('product_tags');
        $this->tagsHelper()->sortOrderInTable($columnName, 'asc', $this->_getControlXpath('pageelement', 'tags_grid'));
        $tableValues = array();
        $tableValues = $this->tagsHelper()->getInfoInTable(
            $tableValues,
            $this->_getControlXpath('pageelement', 'tags_grid')
        );
        //Check sort order
        foreach ($tableValues as $key => $row) {
            $volume[$key] = strtolower($row[$columnName]);
        }
        $tableValuesNew = $tableValues;
        array_multisort($volume, SORT_ASC, SORT_STRING, $tableValuesNew);
        $this->assertEquals($tableValues, $tableValuesNew);
    }
    public function tagSearchNameDataProvider()
    {
        return array(
            array('Status'),
            array('Tag Name')
        );
    }

}