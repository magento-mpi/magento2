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
class Community2_Mage_Tags_RssCreateTest extends Mage_Selenium_TestCase
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
        //Enable Rss Feeds System Configuration - Enable RSS and Tags Products
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('Catalog/enable_tag_rss');
        //Data
        $userData = array();
        $userData[1] = $this->loadDataSet('Customers', 'generic_customer_account');
        //Steps
        $this->navigate('manage_customers');
        $this->customerHelper()->createCustomer($userData[1]);
        $this->assertMessagePresent('success', 'success_saved_customer');
        $simple = array();
        $category = array();
        for ($i = 0; $i < 3; $i++) {
            $productData = $this->productHelper()->createSimpleProduct(true);
            $simple[] = $productData['simple']['product_name'];
            $category[] = $productData['category']['path'];
        }
        $this->reindexInvalidedData();
        $this->flushCache();
        $userData[1] = array('email' => $userData[1]['email'], 'password' => $userData[1]['password']);
        return array('user'     => $userData,
            'simple'   => $simple,
            'category' => $category);
    }
    /**
     * Tags Rss Feed
     *
     * @param string $tag
     * @param integer $product
     * @param array $testData
     *
     * @test
     * @dataProvider tagNameDataProvider
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-2288
     */
    public function enabledRssTags($tag, $product, $testData)
    {
        //Setup
        $this->customerHelper()->frontLoginCustomer($testData['user'][1]);
        $this->productHelper()->frontOpenProduct($testData['simple'][$product]);
        //Steps
        $this->tagsHelper()->frontendAddTag($tag);
        $this->loginAdminUser();
        $this->navigate('all_tags');
        $this->tagsHelper()->changeTagsStatus(array(array('tag_name' => $tag)), 'Approved');
        $this->reindexAllData();
        $this->flushCache();
        $this->customerHelper()->frontLoginCustomer($testData['user'][1]);
        $this->productHelper()->frontOpenProduct($testData['simple'][$product]);
        $this->tagsHelper()->frontendTagVerificationInCategory($tag,
                                                               $testData['simple'][$product],
                                                               $testData['category'][$product]);
        //Get Rss content
        $feed = $this->rssFeedsHelper()->getRssFeeds('pageelement', 'tag_rss_feeds');
        $rssItems = $this->rssFeedsHelper()->getRssItems($feed);
        //Check items
        $this->assertEquals(count($rssItems), $product+1,
            'Tag Rss Feed contains incorrect amount of Product items');
        $this->assertEquals($testData['simple'][$product], $rssItems[$product]['title'],
            'Tag Rss Feed contains incorrect Product item');
    }
    public function tagNameDataProvider()
    {
        $tagName = $this->generate('string', 4, ':alpha:');
        return array(
            array($tagName, 'product#' => 0),
            array($tagName, 'product#' => 1),
            array($tagName, 'product#' => 2)
        );
    }
    /**
     * Tags Rss Feed
     *
     * @param array $testData
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-2290
     */
    public function disabledRssTags($testData)
    {
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('Catalog/disable_tag_rss');
        $this->customerHelper()->frontLoginCustomer($testData['user'][1]);
        $this->productHelper()->frontOpenProduct($testData['simple'][0]);
        $tag = $this->generate('string', 4, ':alpha:');
        $this->tagsHelper()->frontendAddTag($tag);
        $this->loginAdminUser();
        $this->navigate('all_tags');
        $this->tagsHelper()->changeTagsStatus(array(array('tag_name' => $tag)), 'Approved');

        $this->tagsHelper()->frontendTagVerificationInCategory($tag,
            $testData['simple'][0],
            $testData['category'][0]);
        //Check Rss link
        $this->assertFalse($this->controlIsPresent('pageelement', 'tag_rss_feeds'),
            'Product Tags Rss Feed is present.');
    }
}