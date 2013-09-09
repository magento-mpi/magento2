<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_DesignEditor
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_DesignEditor_Block_Adminhtml_Theme_Selector_SelectorList_AbstractTest
    extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider getSampleCollection
     */
    public function testGetListItems($collection)
    {
        /** @var $listAbstractBlock Magento_DesignEditor_Block_Adminhtml_Theme_Selector_SelectorList_Abstract */
        $listAbstractBlock = $this->getMockForAbstractClass(
            'Magento_DesignEditor_Block_Adminhtml_Theme_Selector_SelectorList_Abstract', array(), '', false, false, true,
            array('getChildBlock')
        );

        $themeMock = $this->getMock('Magento_DesignEditor_Block_Adminhtml_Theme', array(), array(), '', false);

        $listAbstractBlock->setCollection($collection);

        $listAbstractBlock->expects($this->atLeastOnce())
            ->method('getChildBlock')
            ->will($this->returnValue($themeMock));

        $this->assertEquals(2, count($listAbstractBlock->getListItems()));
    }

    /**
     * @return array
     */
    public function getSampleCollection()
    {
        return array(array(array(
            array('first_item'),
            array('second_item')
        )));
    }

    public function testAddAssignButtonHtml()
    {
        /** @var $listAbstractBlock Magento_DesignEditor_Block_Adminhtml_Theme_Selector_SelectorList_Abstract */
        $listAbstractBlock = $this->getMockForAbstractClass(
            'Magento_DesignEditor_Block_Adminhtml_Theme_Selector_SelectorList_Abstract', array(), '', false, false, true,
            array('getChildBlock', 'getLayout')
        );
        /** @var $themeMock Magento_Core_Model_Theme */
        $themeMock = $this->getMock('Magento_Core_Model_Theme', array(), array(), '', false);
        /** @var $themeBlockMock Magento_DesignEditor_Block_Adminhtml_Theme */
        $themeBlockMock = $this->getMock(
            'Magento_DesignEditor_Block_Adminhtml_Theme', array('getTheme'), array(), '', false
        );
        /** @var $layoutMock Magento_Core_Model_Layout */
        $layoutMock  = $this->getMock('Magento_Core_Model_Layout', array('createBlock'), array(), '', false);
        /** @var $buttonMock Magento_Backend_Block_Widget_Button */
        $buttonMock = $this->getMock('Magento_Backend_Block_Widget_Button', array(), array(), '', false);

        $layoutMock->expects($this->once())
            ->method('createBlock')
            ->will($this->returnValue($buttonMock));

        $themeBlockMock->expects($this->once())
            ->method('getTheme')
            ->will($this->returnValue($themeMock));

        $listAbstractBlock->expects($this->once())
            ->method('getLayout')
            ->will($this->returnValue($layoutMock));

        $themeMock->expects($this->once())
            ->method('getId')
            ->will($this->returnValue(1));

        $method = new ReflectionMethod($listAbstractBlock, '_addAssignButtonHtml');
        $method->setAccessible(true);
        $method->invoke($listAbstractBlock, $themeBlockMock);
    }
}
