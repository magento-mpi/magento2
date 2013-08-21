<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_CmsStaticBlocks
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Attribute Set creation tests
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_CmsStaticBlocks_DeleteTest extends Mage_Selenium_TestCase
{
    /**
     * <p>Preconditions:</p>
     * <p>Navigate to CMS -> Static Blocks</p>
     */
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->navigate('manage_cms_static_blocks');
    }

    /**
     * <p>Delete a static block</p>
     *
     * @test
     * @TestlinkId TL-MAGE-3228
     */
    public function deleteNew()
    {
        //Data
        $setData = $this->loadDataSet('CmsStaticBlock', 'new_static_block');
        $blockToDelete = $this->loadDataSet(
            'CmsStaticBlock',
            'search_static_block',
            array('filter_block_identifier' => $setData['block_identifier'])
        );
        //Steps
        $this->cmsStaticBlocksHelper()->createStaticBlock($setData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_block');
        //Steps
        $this->cmsStaticBlocksHelper()->deleteStaticBlock($blockToDelete);
        //Verifying
        $this->assertMessagePresent('success', 'success_deleted_block');
    }
}
