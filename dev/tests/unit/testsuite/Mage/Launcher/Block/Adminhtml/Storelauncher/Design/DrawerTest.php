<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Launcher
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Mage_Launcher_Block_Adminhtml_Storelauncher_Design_Drawer
 */
class Mage_Launcher_Block_Adminhtml_Storelauncher_Design_DrawerTest extends PHPUnit_Framework_TestCase
{
    /**
     * Drawer Block
     *
     * @var Mage_Launcher_Block_Adminhtml_Storelauncher_Design_Drawer
     */
    protected $_drawerBlock;

    public function setUp()
    {
        $objectManagerHelper = new Magento_Test_Helper_ObjectManager($this);

        $layout = $this->getMock('Mage_Core_Model_Layout', array('createBlock'), array(), '', false);

        $layout->expects($this->any())
            ->method('createBlock')
            ->will($this->returnValue(
                $this->getMock('Mage_Backend_Block_Template', array('setTemplate'), array(), '', false)
            )
        );

        $themeService = $this->getMock('Mage_Core_Model_Theme_Service', array('getAllThemes'), array(), '', false);

        $themeService->expects($this->any())
            ->method('getAllThemes')
            ->will($this->returnValue($this->_getThemes()));

        $arguments = array(
            'layout' => $layout,
            'urlBuilder' => $this->getMock('Mage_Backend_Model_Url', array(), array(), '', false),
            'linkTracker' => $this->getMock('Mage_Launcher_Model_LinkTracker', array(), array(), '', false),
            'themeService' => $themeService
        );

        $this->_drawerBlock = $objectManagerHelper->getBlock(
            'Mage_Launcher_Block_Adminhtml_Storelauncher_Design_Drawer',
            $arguments
        );

        $this->_configData = array();
    }

    public function tearDown()
    {
        unset($this->_drawerBlock);
        unset($this->_configData);
    }

    public function testGetThemesBlocks()
    {
        $themesBlocks = $this->_drawerBlock->getThemesBlocks();
        $this->assertEquals(5, count($themesBlocks));
    }

    /**
     * Get mocked themes
     * @return array
     */
    protected function _getThemes()
    {
        $themes = array();
        for ($iterationIndex = 0; $iterationIndex < 5; $iterationIndex++) {
            $themes[] = $this->getMock('Mage_Core_Model_Theme', array(), array(), '', false);
        }
        return $themes;
    }
}
