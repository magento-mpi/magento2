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
 * Tags Validation on the frontend
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_Tags_FrontendManagementTest extends Core_Mage_Tags_TagsFixtureAbstract
{
    /**
     * @return array
     * @test
     * @skipTearDown
     */
    public function preconditionsForTests()
    {
        $this->markTestIncomplete('MAGETWO-1299');
        return parent::_preconditionsForAllTagsTests();
    }

    /**
     * Tag creating with Logged Customer:
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
        $this->productHelper()->frontOpenProduct($testData['simple']);
        //Steps
        $this->tagsHelper()->frontendAddTag($tags);
        //Verification
        $this->assertMessagePresent('success', $message);
        $tags = $this->tagsHelper()->_convertTagsStringToArray($tags);
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
        $this->tagsHelper()->frontendTagVerification($tags, $testData['simple']);
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