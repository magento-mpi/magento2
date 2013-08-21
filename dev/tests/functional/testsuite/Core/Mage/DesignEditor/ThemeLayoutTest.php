<?php
    /**
     * {license_notice}
     *
     * @category    Magento
     * @package     Mage_DesignEditor
     * @subpackage  functional_tests
     * @copyright   {copyright}
     * @license     {license_link}
     */

    /**
     * <p>Theme selector tests</p>
     *
     * @package     selenium
     * @subpackage  tests
     * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
     */
class Core_Mage_DesignEditor_ThemeLayoutTest extends Mage_Selenium_TestCase
{
    /**
     * Elements and blocks names
     */
    const FRAME_ID = 'vde_container_frame';
    const DRAGGABLE_VDE_BLOCK_NAME = 'catalog.compare.sidebar';
    const DRAGGABLE_FRONT_BLOCK_NAME = 'block-compare';
    const CONTENT_VDE_BLOCK_NAME = 'left';
    const CONTENT_FRONT_BLOCK_NAME = 'col-left sidebar';
    const RIGHT_SIDEBAR_FRONT_BLOCK_NAME = 'col-right sidebar';

    /**
     * Store new window handle
     *
     * @var null
     */
    private $_windowId = null;

    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
    }

    protected function tearDownAfterTest()
    {
        //Close 'New' browser window if any
        if ($this->_windowId) {
            $this->closeWindow($this->_windowId);
            $this->_windowId = null;
        }
        //Back to main window
        $this->window('');
    }

    /**
     * Drag and drop
     * @test
     */
    public function dragAndDropBlock()
    {
        //Data
        $this->navigate('design_editor_selector');
        $themeId = $this->designEditorHelper()->assignFromAvailableThemeTab();
        $this->navigate('design_editor_selector');
        $this->designEditorHelper()->assignFromAvailableThemeTab();
        $this->navigate('design_editor_selector');
        $this->addParameter('id', $themeId);
        $this->designEditorHelper()->focusOnThemeElement('button', 'edit_customization_button');
        $this->designEditorHelper()->mouseOver('theme_thumbnail');
        $this->clickButton('edit_customization_button');
        $this->_windowId = $this->selectLastWindow();
        $this->addParameter('id', $themeId);
        $this->validatePage('preview_theme_in_design');

        // Drag block into another container
        $blockDraggable = $this->designEditorHelper()->getBlock(self::DRAGGABLE_VDE_BLOCK_NAME, true);
        $destination = $this->designEditorHelper()->getBlock(self::CONTENT_VDE_BLOCK_NAME, true);
        $this->designEditorHelper()->dragBlock($blockDraggable, $destination);
        $this->assertEquals($destination->attribute('data-name'),
            $this->getContainer($blockDraggable, true)->attribute('data-name')
        );

        // Switch to navigation mode, open required page
        $this->designEditorHelper()->selectModeSwitcher('Disabled');
        $this->clickControl('link', 'advanced_search', false);
        $this->fillFieldset(array('name' => 'test'), 'advanced_search_information');
        $this->clickButton('search', false);

        // Verify that changes are applied
        $container = $this->designEditorHelper()->getContainer(
            $this->designEditorHelper()->getBlock(self::DRAGGABLE_FRONT_BLOCK_NAME)
        );
        $this->assertEquals(self::CONTENT_FRONT_BLOCK_NAME, $container->attribute('class'));
        $this->assertNull($this->vdeHelper()->getBlock(self::REMOVABLE_FRONT_BLOCK_NAME));
    }
}