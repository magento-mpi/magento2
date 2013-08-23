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
class Core_Mage_Tags_CustomerTagsTest extends Core_Mage_Tags_TagsFixtureAbstract
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
     * Backend verification added tag from frontend on the Customer Page
     *
     * @param string $tags
     * @param string $status
     * @param array $testData
     *
     * @test
     * @dataProvider tagNameDataProvider
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-2377, TL-MAGE-2379
     */
    public function addNewTags($tags, $status, $testData)
    {
        $this->markTestIncomplete('MAGETWO-1299');
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

        //Open customer
        foreach ($tags as $tag) {
            if ($status != 'Pending') {
                $this->navigate('all_tags');
                $this->tagsHelper()->changeTagsStatus(array(array('tag_name' => $tag)), $status);
            }
            $tagSearchData = array('tag_name' => $tag, 'status' => $status, 'product_name' => $testData['simple']);
            $customerSearchData = array('customer_email' => $testData['user'][1]['email']);
            $this->navigate('manage_customers');
            $this->assertTrue($this->tagsHelper()->verifyTagCustomer($tagSearchData, $customerSearchData),
                'Product tags verification is failure');
            $this->navigate('all_tags');
            $this->tagsHelper()->openTag(array('tags_status' => $status, 'tag_name' => $tag));
            $this->saveForm('save_tag');
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
     * @TestlinkId TL-MAGE-2378
     */
    public function searchTags($columnName, $testData)
    {
        $this->markTestIncomplete('MAGETWO-2854');
        //Setup
        $this->loginAdminUser();
        $this->navigate('manage_customers');
        $this->customerHelper()->openCustomer(array('customer_email' => $testData['user'][1]['email']));
        $this->openTab('product_tags');
        $this->sortTableOrderByColumn($columnName, 'asc', $this->_getControlXpath('pageelement', 'tags_grid'));
        $tableValues = $this->getInfoInTable(array(), $this->_getControlXpath('pageelement', 'tags_grid'));
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