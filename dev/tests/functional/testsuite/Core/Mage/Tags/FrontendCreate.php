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
class Core_Mage_Tags_FrontendCreateTest extends Mage_Selenium_TestCase
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
        $userData = $this->loadDataSet('Customers', 'customer_account_register');
        //Steps and Verification
        $simple = $this->productHelper()->createSimpleProduct(true);
        $this->reindexInvalidedData();
        $this->flushCache();
        $this->frontend('customer_login');
        $this->customerHelper()->registerCustomer($userData);
        $this->assertMessagePresent('success', 'success_registration');
        $this->logoutCustomer();

        return array(
            'user' => array('email' => $userData['email'], 'password' => $userData['password']),
            'simple' => $simple['simple']['product_name'],
            'category' => $simple['category']['path']
        );
    }

    /**
     * <p>Tag creating with Logged Customer</p>
     *
     * @param string $tags
     * @param array $testData
     *
     * @test
     * @dataProvider tagNameDataProvider
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-3651
     */
    public function frontendTagVerificationLoggedCustomer($tags, $testData)
    {
        $this->markTestIncomplete('MAGETWO-1299');
        //Setup
        $this->customerHelper()->frontLoginCustomer($testData['user']);
        $this->productHelper()->frontOpenProduct($testData['simple']);
        //Steps
        $this->tagsHelper()->frontendAddTag($tags);
        //Verification
        $this->assertMessagePresent('success', 'tag_accepted_success');
        $this->tagsHelper()->frontendTagVerification($tags, $testData['simple']);
    }

    public function tagNameDataProvider()
    {
        return array(
            //1 simple word
            array($this->generate('string', 4, ':alpha:')),
            //1 tag enclosed within quotes
            array("'" . $this->generate('string', 4, ':alpha:') . "'"),
            //2 tags separated with a space
            array($this->generate('string', 4, ':alpha:') . ' ' . $this->generate('string', 7, ':alpha:')),
            //1 tag with a space; enclosed within quotes
            array("'" . $this->generate('string', 4, ':alpha:') . ' ' . $this->generate('string', 7, ':alpha:') . "'"),
            //3 tags = 1 word + 1 phrase with a space + 1 word; enclosed within quotes
            array($this->generate('string', 4, ':alpha:') . ' ' . "'" . $this->generate('string', 4, ':alpha:') . ' '
                . $this->generate('string', 7, ':alpha:') . "'" . ' ' . $this->generate('string', 4, ':alpha:'))
        );
    }

    /**
     * Tag creating with Not Logged Customer
     *
     * @param array $testData
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-3652
     */
    public function frontendTagVerificationNotLoggedCustomer($testData)
    {
        //Data
        $tag = $this->generate('string', 8, ':alpha:');
        $searchTag = $this->loadDataSet('Tag', 'backend_search_tag', array('tag_name' => $tag));
        //Setup
        $this->frontend();
        $this->productHelper()->frontOpenProduct($testData['simple']);
        //Steps
        $this->tagsHelper()->frontendAddTag($tag);
        //Verification
        $this->assertTrue($this->checkCurrentPage('customer_login'), $this->getParsedMessages());
        $this->loginAdminUser();
        $this->navigate('all_tags');
        $this->assertNull($this->search($searchTag, 'tags_grid'), $this->getMessagesOnPage());
        $this->customerHelper()->frontLoginCustomer($testData['user']);
    }

    /**
     * <p>Tags Verification in Category</p>
     *
     * @param array $testData
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-3650
     */
    public function frontendTagVerificationInCategory($testData)
    {
        $this->markTestIncomplete('MAGETWO-1299');
        //Data
        $tag = $this->generate('string', 10, ':alpha:');
        $tagToApprove = $this->loadDataSet('Tag', 'backend_search_tag', array('tag_name' => $tag));
        //Setup
        $this->customerHelper()->frontLoginCustomer($testData['user']);
        $this->productHelper()->frontOpenProduct($testData['simple']);
        //Steps
        $this->tagsHelper()->frontendAddTag($tag);
        //Verification
        $this->assertMessagePresent('success', 'tag_accepted_success');
        //Steps
        $this->loginAdminUser();
        $this->navigate('all_tags');
        $this->tagsHelper()->changeTagsStatus(array($tagToApprove), 'Approved');
        //Verification
        $this->frontend();
        $this->tagsHelper()->frontendTagVerificationInCategory($tag, $testData['simple'], $testData['category']);
    }
}