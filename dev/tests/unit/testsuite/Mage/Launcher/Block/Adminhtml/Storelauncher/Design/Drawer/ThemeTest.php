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
 * Test class for Mage_Launcher_Block_Adminhtml_Storelauncher_Design_Drawer_Theme
 */
class Mage_Launcher_Block_Adminhtml_Storelauncher_Design_Drawer_ThemeTest extends PHPUnit_Framework_TestCase
{
    public function testGetThemesBlocks()
    {
        $objectManagerHelper = new Magento_Test_Helper_ObjectManager($this);

        $layout = $this->getMock('Mage_Core_Model_Layout', array(), array(), '', false, false);
        $urlBuilder = $this->getMock('Mage_Backend_Model_Url', array('getUrl'), array(), '', false);
        $urlBuilder->expects($this->once())
            ->method('getUrl')
            ->with('adminhtml/system_design_editor/preview', array(
                'theme_id' => 4,
                'mode' => Mage_DesignEditor_Model_State::MODE_NAVIGATION
            ))
            ->will($this->returnValue('admin/system_design_editor/preview/theme_id/4/mode/navigation'));

        $arguments = array(
            'layout' => $layout,
            'urlBuilder' => $urlBuilder,
        );

        /** @var $themeBlock Mage_Launcher_Block_Adminhtml_Storelauncher_Design_Drawer_Theme */
        $themeBlock = $objectManagerHelper->getObject(
            'Mage_Launcher_Block_Adminhtml_Storelauncher_Design_Drawer_Theme',
            $arguments
        );

        $url = $themeBlock->getPreviewUrl(4);
        $this->assertRegExp('/theme_id\/\d+/', $url);
    }
}
