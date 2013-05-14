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
class Core_Mage_Vde_TemporarySaveTest extends Mage_Selenium_TestCase
{
    /**
     * Elements and blocks names
     */
    const FRAME_ID = 'vde_container_frame';
    const DRAGGABLE_VDE_BLOCK_NAME = 'catalog.compare.sidebar';
    const DRAGGABLE_FRONT_BLOCK_NAME = 'block-compare';
    const REMOVABLE_VDE_BLOCK_NAME = 'right.poll';
    const REMOVABLE_FRONT_BLOCK_NAME = 'block-poll';
    const CONTENT_VDE_BLOCK_NAME = 'content';
    const CONTENT_FRONT_BLOCK_NAME = 'col-main';
    const RIGHT_SIDEBAR_FRONT_BLOCK_NAME = 'col-right sidebar';

    public function setUpBeforeTests()
    {
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
     * @TestlinkId TL-MAGE-6568
     * @author iuliia.babenko
     */
    public function temporarySaveDragAndRemove()
    {
        // Open VDE in design mode, select required page type
        $this->vdeHelper()
            ->switchToDesignMode()
            ->expandPageHandle('All Pages')
            ->expandPageHandle('Advanced Search Form')
            ->selectPageHandle('Advanced Search Result');

        // Drag block into another container
        $blockDraggable = $this->vdeHelper()->getBlock(self::DRAGGABLE_VDE_BLOCK_NAME, true);
        $destination = $this->vdeHelper()->getBlock(self::CONTENT_VDE_BLOCK_NAME, true);
        $this->vdeHelper()->dragBlock($blockDraggable, $destination);

        // Remove block
        $blockRemovable = $this->vdeHelper()->getBlock(self::REMOVABLE_VDE_BLOCK_NAME, true);
        $this->vdeHelper()->removeBlock($blockRemovable);

        // Switch to navigation mode, open required page
        $this->vdeHelper()->switchToNavigationMode();
        $this->frame(self::FRAME_ID);
        $this->vdeHelper()->setVdePage('navigation', 'advanced_search');
        $this->clickControl('link', 'advanced_search', false);
        $this->fillFieldset(array('name' => 'test'), 'advanced_search_information');
        $this->clickButton('search', false);

        // Verify that changes are applied
        $container = $this->vdeHelper()->getContainer(
            $this->vdeHelper()->getBlock(self::DRAGGABLE_FRONT_BLOCK_NAME)
        );
        $this->assertEquals(self::CONTENT_FRONT_BLOCK_NAME, $container->attribute('class'));
        $this->assertNull($this->vdeHelper()->getBlock(self::REMOVABLE_FRONT_BLOCK_NAME));

        // Switch to design mode
        $this->vdeHelper()
            ->switchToDesignMode()
            ->expandPageHandle('All Pages')
            ->expandPageHandle('Advanced Search Form')
            ->selectPageHandle('Advanced Search Result');

        // Verify that changes are still present
        $blockDraggable = $this->vdeHelper()->getBlock(self::DRAGGABLE_VDE_BLOCK_NAME, true);
        $this->assertEquals(self::CONTENT_VDE_BLOCK_NAME,
            $this->vdeHelper()->getContainer($blockDraggable, true)->attribute('data-name')
        );
        $this->assertNull($this->vdeHelper()->getBlock(self::REMOVABLE_VDE_BLOCK_NAME, true));

        // Open required page on frontend
        $this->window('');
        $this->frontend('advanced_search');
        $this->fillFieldset(array('name' => 'test'), 'advanced_search_information');
        $this->clickButton('search', false);

        // Verify that changes were not applied
        $container = $this->vdeHelper()->getContainer(
            $this->vdeHelper()->getBlock(self::DRAGGABLE_FRONT_BLOCK_NAME)
        );
        $this->assertEquals(self::RIGHT_SIDEBAR_FRONT_BLOCK_NAME, $container->attribute('class'));
        $this->assertNotNull($this->vdeHelper()->getBlock(self::REMOVABLE_FRONT_BLOCK_NAME));
    }
}
