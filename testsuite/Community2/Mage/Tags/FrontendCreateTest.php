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
 * Tags Validation on the frontend
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Community2_Mage_Tags_FrontendCreateTest extends Mage_Selenium_TestCase
{
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
    }

    protected function tearDownAfterTest()
    {
        $this->loginAdminUser();
        $this->navigate('all_tags');
        $this->tagsHelper()->deleteAllTags();
        $this->logoutCustomer();
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
     * Tag creating with Logged Customer:
     *  Adding single new tag
     *  Adding several single new tags
     *  Adding new tag phrase
     *  Adding single tag and tag phrase
     *  Adding approved tag
     *
     * @param string $tags
     * @param string $status
     * @param array $testData
     *
     * @test
     * @dataProvider tagNameDataProvider
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-2263, TL-MAGE-2267, TL-MAGE-2268, TL-MAGE-2269, TL-MAGE-2270
     */
    public function frontendTagVerificationLoggedCustomer($tags, $status, $testData)
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
        if ($status == 'Approved') {
            $this->navigate('all_tags');
            foreach ($tags as $tag) {
                $this->tagsHelper()->changeTagsStatus(array(array('tag_name' => $tag)), $status);
            }
        }
        foreach ($tags as $tag) {
            $this->navigate('all_tags');
            $this->tagsHelper()->verifyTag(array('tag_name' => $tag, 'status' => $status));
        }
    }

    public function tagNameDataProvider()
    {
        return array(
            //TL-MAGE-2263 simple word
            array($this->generate('string', 4, ':alpha:'), 'Pending'),
            //TL-MAGE-2267 several simple words
            array($this->generate('string', 4, ':alpha:') . ' ' . $this->generate('string', 7, ':alpha:'), 'Pending'),
            //TL-MAGE-2268 simple (tag phrase enclosed within quotes)
            array("'" . $this->generate('string', 4, ':alpha:') . "'", 'Pending'),
            //TL-MAGE-2269 simple word and tag phrase
            array($this->generate('string', 4, ':alpha:') . ' ' .
                "'" . $this->generate('string', 4, ':alpha:') . ' ' . $this->generate('string', 7, ':alpha:') . "'",
                'Pending'),
            //TL-MAGE-2270 simple word
            array($this->generate('string', 4, ':alpha:'), 'Approved')
        );
    }
    /**
     * Tag creating with Logged Customer
     *  Adding already assigned tag by the same customer
     *  Adding already assigned tag by the another customer
     *
     * @param string $tags
     * @param array $testData
     * @param int $customer
     * @param string $message
     *
     * @test
     * @dataProvider tagApprovedNameDataProvider
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-2272, TL-MAGE-2274
     */
    public function frontendApprovedTagVerificationLoggedCustomer($tags, $customer, $message,  $testData)
    {
        //Setup
        $this->customerHelper()->frontLoginCustomer($testData['user'][$customer]);
        $this->productHelper()->frontOpenProduct($testData['simple']);
        //Steps
        $this->tagsHelper()->frontendAddTag($tags);
        //Verification
        $this->assertMessagePresent('success', $message);
        $this->tagsHelper()->frontendTagVerification($tags, $testData['simple']);
        $this->loginAdminUser();
        $this->navigate('all_tags');
        $this->tagsHelper()->changeTagsStatus(array(array('tag_name' => $tags)), 'Approved');
        $this->assertTrue((bool) $this->search(array('tag_name' => $tags, 'status' => 'Approved')),
            'The same Tag has been added twice ' . print_r($tags, true));
        $this->tagsHelper()->verifyTag(array('tag_name' => $tags, 'status' => 'Approved'));
    }

    public function tagApprovedNameDataProvider()
    {
        $approvedTag = $this->generate('string', 4, ':alpha:');
        return array(
            //TL-MAGE-2272, TL-MAGE-2274 simple word
            array($approvedTag, 1, 'tag_accepted_success'),
            array($approvedTag, 1, 'tag_already_success'),
            array($approvedTag, 2, 'tag_already_success'),
        );
    }
}