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
 * Backend Delete Tags tests
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_Tags_BackendDeleteTest extends Mage_Selenium_TestCase
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

    /**
     * <p>Deleting a new tag</p>
     *
     * @test
     * @TestlinkId TL-MAGE-3498
     */
    public function deleteNew()
    {
        //Setup
        $setData = $this->loadDataSet('Tag', 'backend_new_tag');
        //Steps
        $this->tagsHelper()->addTag($setData);
        $this->assertTrue($this->checkCurrentPage('all_tags'), $this->getParsedMessages());
        $this->tagsHelper()->deleteTag(array('tag_name' => $setData['tag_name']));
        //Verify
        $this->assertTrue($this->checkCurrentPage('all_tags'), $this->getParsedMessages());
        $this->assertMessagePresent('success', 'success_deleted_tag');
    }
}
