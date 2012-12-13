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

    protected function setUp()
    {
        $helper = new Magento_Test_Helper_ObjectManager($this);

        $urlBuilder = $this->getMock('Mage_Backend_Model_Url', array('getUrl'), array(), '', false);
        $urlBuilder->expects($this->once())
            ->method('getUrl')
            ->will($this->returnArgument(0));

        $arguments = array(
            'urlBuilder' => $urlBuilder
        );

        /** @var $_block Mage_DesignEditor_Block_Adminhtml_Editor_Toolbar_Buttons */
        $this->_block = $helper->getBlock('Mage_DesignEditor_Block_Adminhtml_Editor_Toolbar_Buttons', $arguments);
    }

    public function testGetViewLayoutUrl()
    {
        $this->assertEquals('*/*/getLayoutUpdate', $this->_block->getViewLayoutUrl());
    }

    public function testGetBackUrl()
    {
        $this->assertEquals('*/*/', $this->_block->getBackUrl());
    }
}
