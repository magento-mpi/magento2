<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
/**
 * @method Core_Mage_Vde_Helper vdeHelper() vdeHelper()
 */

class Core_Mage_Vde_SaveChangesTest extends Mage_Selenium_TestCase
{
    /**
     * Elements and blocks names
     */
    const FRAME_ID                   = 'vde_container_frame';
    const DRAGGABLE_VDE_BLOCK_NAME   = 'catalog.compare.sidebar';
    const DRAGGABLE_FRONT_BLOCK_NAME = 'block-compare';
    const REMOVABLE_VDE_BLOCK_NAME   = 'right.poll';
    const REMOVABLE_FRONT_BLOCK_NAME = 'block-poll';
    const CONTENT_VDE_BLOCK_NAME     = 'content';
    const CONTENT_FRONT_BLOCK_NAME   = 'col-main';

    public function setUpBeforeTests()
    {
        $this->currentWindow()->maximize();
        $this->loginAdminUser();
        $this->admin('design_editor_selector');
        $this->vdeHelper()->openThemeDemo();
    }

    protected function tearDownAfterTest()
    {
        $this->window('');
    }

    /**
     * @test
     * @TestlinkId TL-MAGE-5988, TL-MAGE-5989
     * @author irina.glazunova
     */
    public function removeExistingBlock()
    {
        $this->markTestIncomplete('Test can not be implemented due to affect Frontend design');
        // Open the proper page in the Design Mode
        $this->vdeHelper()
            ->switchToDesignMode()
            ->expandPageHandle('All Pages')
            ->selectPageHandle('Quick Search Form');


        $dragBlock = $this->vdeHelper()->getBlock(self::DRAGGABLE_VDE_BLOCK_NAME, true);
        $destination = $this->vdeHelper()->getBlock(self::CONTENT_VDE_BLOCK_NAME, true);

        // Drag the block "Compare Products"
        $this->vdeHelper()->dragBlock($dragBlock, $destination);

        // Remove the block "Community Pool"
        $block = $this->vdeHelper()->getBlock(self::REMOVABLE_VDE_BLOCK_NAME, true);
        $this->vdeHelper()->removeBlock($block);


        $this->window('');

        // Save and apply the changes
        $this->clickButtonAndConfirm('assign_theme', 'confirm_assign', false);

        // Verify that changes are applied on frontend
        $this->frontend('quick_search');

        $dragBlock = $this->vdeHelper()->getBlock(self::DRAGGABLE_FRONT_BLOCK_NAME);
        $this->assertEquals(self::CONTENT_FRONT_BLOCK_NAME,
            $this->vdeHelper()->getContainer($dragBlock, true)->attribute('data-name')
        );
        $this->assertNull($this->vdeHelper()->getBlock(self::REMOVABLE_FRONT_BLOCK_NAME));
    }
}
