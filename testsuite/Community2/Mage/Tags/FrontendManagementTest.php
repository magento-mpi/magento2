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
class Community2_Mage_Tags_FrontendManagementTest extends Mage_Selenium_TestCase
{
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
    }

    protected function tearDownAfterTest()
    {
        $this->loginAdminUser();
        $this->navigate('all_tags');
        //$this->tagsHelper()->deleteAllTags();
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
        $simple = array();
        $simple[1] = $this->productHelper()->createSimpleProduct(true);
        $simple[2] = $this->productHelper()->createSimpleProduct(true);
        $this->reindexInvalidedData();
        $this->flushCache();
        $userData[1] = array('email' => $userData[1]['email'], 'password' => $userData[1]['password']);
        $userData[2] = array('email' => $userData[2]['email'], 'password' => $userData[2]['password']);
        $simple[1] = $simple[1]['simple']['product_name'];
        $simple[2] = $simple[2]['simple']['product_name'];
        return array('user'     => $userData,
                     'simple'   => $simple);
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
     * @param string $message
     * @param array $testData
     *
     * @test
     * @dataProvider tagNameDataProvider
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-2292
     */
    public function frontendTagManagementLoggedCustomer($tags, $status, $message, $testData)
    {
        //Setup
        $this->customerHelper()->frontLoginCustomer($testData['user'][1]);
        $this->productHelper()->frontOpenProduct($testData['simple'][1]);
        //Steps
        $this->tagsHelper()->frontendAddTag($tags);
        //Verification
        $this->verifyMessagePresent('success', $message);
        $tags = $this->tagsHelper()->convertTagsStringToArray($tags);
        $this->loginAdminUser();
        if ($status != 'Pending') {
            $this->navigate('all_tags');
            foreach ($tags as $tag) {
                $this->tagsHelper()->changeTagsStatus(array(array('tag_name' => $tag)), $status);
            }
        }
        foreach ($tags as $tag) {
            $this->navigate('all_tags');
            $this->tagsHelper()->verifyTag(array('tag_name' => $tag, 'status' => $status));
        }
        $this->frontend();
        $this->tagsHelper()->frontendTagVerification($tags, $testData['simple'][1]);
    }

    public function tagNameDataProvider()
    {
        $tagsData = array();
        $tagsData[1] = $this->generate('string', 4, ':alpha:');
        $tagsData[2] = $this->generate('string', 4, ':alpha:');
        $tagsData[3] = $this->generate('string', 4, ':alpha:');
        return array(
            //TL-MAGE-2292 simple word
            array($tagsData[1], 'Approved', 'tag_accepted_success'),
            //TL-MAGE-2292 simple word
            array($tagsData[2], 'Approved', 'tag_accepted_success'),
            //TL-MAGE-2293 simple word
            array($tagsData[2], 'Approved', 'tag_already_success'),
            //TL-MAGE-2294 simple word
            array($tagsData[2], 'Disabled', 'tag_already_success')
        );
    }
}