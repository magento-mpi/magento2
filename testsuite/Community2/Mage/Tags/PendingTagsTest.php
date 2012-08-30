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
class Community2_Mage_Tags_PendingCreateTest extends Mage_Selenium_TestCase
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
     * Pending status for new customer's tag
     *
     * @param string $tags
     * @param string $status
     * @param array $testData
     *
     * @test
     * @dataProvider tagNameDataProvider
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-2327
     */
    public function addFromFrontendTags($tags, $status, $testData)
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
            $this->navigate('pending_tags');
            foreach ($tags as $tag) {
                $this->tagsHelper()->changeTagsStatus(array(array('tag_name' => $tag)), $status);
            }
        }
    }
    public function tagNameDataProvider()
    {
        return array(
            array($this->generate('string', 4, ':alpha:'), 'Approved'),
            array($this->generate('string', 4, ':alpha:'), 'Disabled'),
            array($this->generate('string', 4, ':alpha:'), 'Pending')
        );
    }
    /**
     * Search pending customer's tag
     *
     * @param string $tags
     * @param string $status
     * @param array $testData
     *
     * @test
     * @dataProvider tagSearchSelectedDataProvider
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-2343
     */
    public function searchSelectedTags($tags, $status, $testData)
    {
        //Setup
        $this->customerHelper()->frontLoginCustomer($testData['user'][1]);
        $this->productHelper()->frontOpenProduct($testData['simple']);
        //Steps
        $this->tagsHelper()->frontendAddTag($tags);
        //Verification
        $this->assertMessagePresent('success', 'tag_accepted_success');
        $this->tagsHelper()->frontendTagVerification($tags, $testData['simple']);
        $this->loginAdminUser();
        $this->navigate('pending_tags');
        $this->searchAndChoose(array('tag_name' => $tags));
        $this->fillForm(array('filter_massaction' => $status));
        $this->clickButton('search', false);
        $this->waitForAjax();
        if ($status == 'No') {
            $this->assertFalse((bool) $this->search(array('tag_name' => $tags), null, false),
                "Filter {$status} works incorrect");
        } else {
            $this->assertTrue((bool) $this->search(array('tag_name' => $tags), null, false),
                "Filter {$status} works incorrect");
        }
    }
    public function tagSearchSelectedDataProvider()
    {
        return array(
            array($this->generate('string', 4, ':alpha:'), 'Any'),
            array($this->generate('string', 4, ':alpha:'), 'No'),
            array($this->generate('string', 4, ':alpha:'), 'Yes')
        );
    }
    /**
     * Search pending customer's tag
     *
     * @param string $tags
     * @param string $status
     * @param array $testData
     *
     * @test
     * @dataProvider tagSearchNameDataProvider
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-2343
     */
    public function searchNameTags($tags, $status, $testData)
    {
        if ($status) {
            //Setup
            $this->customerHelper()->frontLoginCustomer($testData['user'][1]);
            $this->productHelper()->frontOpenProduct($testData['simple']);
            //Steps
            $this->tagsHelper()->frontendAddTag($tags);
            //Verification
            $this->assertMessagePresent('success', 'tag_accepted_success');
            $this->tagsHelper()->frontendTagVerification($tags, $testData['simple']);
        }
        $this->loginAdminUser();
        $this->navigate('pending_tags');
        $this->clickButton('reset_filter', false);
        $this->waitForAjax();
        $this->fillForm(array('tag_name' => $tags));
        $this->clickButton('search', false);
        $this->waitForAjax();
        $this->assertTrue($status == (bool) $this->search(array('tag_name' => $tags), null, false),
                "Filter by Name {$tags} works incorrect");
    }
    public function tagSearchNameDataProvider()
    {
        return array(
            array($this->generate('string', 4, ':alpha:'), true),
            array($this->generate('string', 4, ':alpha:'), false)
        );
    }
    /**
     * Edit pending customer's tag
     *
     * @param string $tags
     * @param string $status
     * @param array $testData
     *
     * @test
     * @dataProvider tagEditDataProvider
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-2344
     */
    public function editTags($tags, $status, $testData)
    {
        //Setup
        $this->customerHelper()->frontLoginCustomer($testData['user'][1]);
        $this->productHelper()->frontOpenProduct($testData['simple']);
        //Steps
        $this->tagsHelper()->frontendAddTag($tags);
        //Verification
        $this->assertMessagePresent('success', 'tag_accepted_success');
        $this->tagsHelper()->frontendTagVerification($tags, $testData['simple']);
        $this->loginAdminUser();
        $this->navigate('pending_tags');
        $this->tagsHelper()->openTag(array('tag_name' => $tags));
        $this->tagsHelper()->fillForm(array('tag_status' => $status));
        $this->tagsHelper()->saveForm('save_tag');
        $this->assertMessagePresent('success', 'success_saved_tag');
        $this->assertFalse((bool) $this->search(array('tag_name' => $tags)),
            "Edit tags and change status {$tags} work incorrect");
        $this->customerHelper()->frontLoginCustomer($testData['user'][1]);
        $this->tagsHelper()->frontendTagVerification($tags, $testData['simple']);
    }
    public function tagEditDataProvider()
    {
        return array(
            array($this->generate('string', 4, ':alpha:'), 'Approved')
        );
    }
}