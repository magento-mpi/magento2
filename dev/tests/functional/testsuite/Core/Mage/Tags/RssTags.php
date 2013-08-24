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
class Core_Mage_Tags_RssTagsTest extends Core_Mage_Tags_TagsFixtureAbstract
{
    /**
     * @return array
     * @test
     * @skipTearDown
     */
    public function preconditionsForTests()
    {
        return parent::_preconditionsForRssTests();
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
        $this->frontend();
        $this->tagsHelper()
            ->frontendTagVerificationInCategory($tag, $testData['simple'][$product], $testData['category'][$product]);
        //Get Rss content
        $feed = $this->rssFeedsHelper()->getRssFeeds('pageelement', 'tag_rss_feeds');
        $rssItems = $this->rssFeedsHelper()->getRssItems($feed);
        //Check items
        $this->assertEquals(count($rssItems), $product + 1, 'Tag Rss Feed contains incorrect amount of Product items');
        $this->assertEquals($testData['simple'][$product], $rssItems[$product]['title'],
            'Tag Rss Feed contains incorrect Product item');
    }

    public function tagNameDataProvider()
    {
        $tagName = $this->generate('string', 4, ':alpha:');
        return array(array($tagName, 'product#' => 0), array($tagName, 'product#' => 1),
                     array($tagName, 'product#' => 2));
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
        $this->frontend();
        $this->tagsHelper()->frontendTagVerificationInCategory($tag, $testData['simple'][0], $testData['category'][0]);
        //Check Rss link
        $this->assertFalse($this->controlIsPresent('pageelement', 'tag_rss_feeds'),
            'Product Tags Rss Feed is present.');
    }
}