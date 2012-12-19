<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_DesignEditor
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_DesignEditor_Block_Adminhtml_Editor_Toolbar_ButtonsTest extends PHPUnit_Framework_TestCase
{
    /**
     * VDE toolbar buttons block
     *
     * @var Mage_DesignEditor_Block_Adminhtml_Editor_Toolbar_Buttons
     */
    protected $_block;

    /**
     * @var Mage_Backend_Model_Url
     */
    protected $_urlBuilder;

    protected function setUp()
    {
        $helper = new Magento_Test_Helper_ObjectManager($this);

        $this->_urlBuilder = $this->getMock('Mage_Backend_Model_Url', array('getUrl'), array(), '', false);

        $arguments = array(
            'urlBuilder' => $this->_urlBuilder
        );

        /** @var $_block Mage_DesignEditor_Block_Adminhtml_Editor_Toolbar_Buttons */
        $this->_block = $helper->getBlock('Mage_DesignEditor_Block_Adminhtml_Editor_Toolbar_Buttons', $arguments);
    }

    public function testGetThemeId()
    {
        $this->_block->setThemeId(1);
        $this->assertEquals(1, $this->_block->getThemeId());
    }

    public function testSetThemeId()
    {
        $this->_block->setThemeId(2);
        $this->assertAttributeEquals(2, '_themeId', $this->_block);
    }

    public function testGetMode()
    {
        $this->_block->setMode(0);
        $this->assertEquals(0, $this->_block->getMode());
    }

    public function testSetMode()
    {
        $this->_block->setMode(1);
        $this->assertAttributeEquals(1, '_mode', $this->_block);
    }

    public function testGetViewLayoutUrl()
    {
        $this->_urlBuilder->expects($this->once())
            ->method('getUrl')
            ->will($this->returnArgument(0));
        $this->assertEquals('*/*/getLayoutUpdate', $this->_block->getViewLayoutUrl());
    }

    public function testGetBackUrl()
    {
        $this->_urlBuilder->expects($this->once())
            ->method('getUrl')
            ->will($this->returnArgument(0));
        $this->assertEquals('*/*/', $this->_block->getBackUrl());
    }

    public function testGetNavigationModeUrl()
    {
        $this->_block->setThemeId(2);

        $this->_urlBuilder->expects($this->once())
            ->method('getUrl')
            ->with('*/*/launch', array('mode' => 1, 'theme_id' => 2))
            ->will($this->returnValue('*/*/launch/mode/1/theme_id/2/'));

        $this->assertEquals('*/*/launch/mode/1/theme_id/2/', $this->_block->getNavigationModeUrl());
    }

    public function testGetDesignModeUrl()
    {
        $this->_block->setThemeId(3);

        $this->_urlBuilder->expects($this->once())
            ->method('getUrl')
            ->with('*/*/launch', array('mode' => 0, 'theme_id' => 3))
            ->will($this->returnValue('*/*/launch/mode/0/theme_id/3/'));

        $this->assertEquals('*/*/launch/mode/0/theme_id/3/', $this->_block->getDesignModeUrl());
    }

    public function testIsNavigationMode()
    {
        $this->_block->setMode(1);
        $this->assertTrue($this->_block->isNavigationMode());

        $this->_block->setMode(0);
        $this->assertFalse($this->_block->isNavigationMode());
    }
}
