<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\DesignEditor\Block\Adminhtml\Theme\Selector\SelectorList;

class AbstractTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider getSampleCollection
     */
    public function testGetListItems($collection)
    {
        /** @var $listAbstractBlock
         *      \Magento\DesignEditor\Block\Adminhtml\Theme\Selector\SelectorList\AbstractSelectorList */
        $listAbstractBlock = $this->getMockForAbstractClass(
            'Magento\DesignEditor\Block\Adminhtml\Theme\Selector\SelectorList\AbstractSelectorList',
            array(),
            '',
            false,
            false,
            true,
            array('getChildBlock', 'getLayout')
        );

        $themeMock = $this->getMock('Magento\DesignEditor\Block\Adminhtml\Theme', array(), array(), '', false);

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
        return array(array(array(array('first_item'), array('second_item'))));
    }

    public function testAddAssignButtonHtml()
    {
        /** @var $listAbstractBlock
         *      \Magento\DesignEditor\Block\Adminhtml\Theme\Selector\SelectorList\AbstractSelectorList */
        $listAbstractBlock = $this->getMockForAbstractClass(
            'Magento\DesignEditor\Block\Adminhtml\Theme\Selector\SelectorList\AbstractSelectorList',
            array(),
            '',
            false,
            false,
            true,
            array('getChildBlock', 'getLayout')
        );
        /** @var $themeMock \Magento\Core\Model\Theme */
        $themeMock = $this->getMock('Magento\Core\Model\Theme', array(), array(), '', false);
        /** @var $themeBlockMock \Magento\DesignEditor\Block\Adminhtml\Theme */
        $themeBlockMock = $this->getMock(
            'Magento\DesignEditor\Block\Adminhtml\Theme',
            array('getTheme'),
            array(),
            '',
            false
        );
        /** @var $layoutMock \Magento\Framework\View\LayoutInterface */
        $layoutMock = $this->getMock('Magento\Framework\View\Layout', array('createBlock'), array(), '', false);
        /** @var $buttonMock \Magento\Backend\Block\Widget\Button */
        $buttonMock = $this->getMock('Magento\Backend\Block\Widget\Button', array(), array(), '', false);

        $layoutMock->expects($this->once())->method('createBlock')->will($this->returnValue($buttonMock));

        $themeBlockMock->expects($this->once())->method('getTheme')->will($this->returnValue($themeMock));

        $listAbstractBlock->expects($this->once())->method('getLayout')->will($this->returnValue($layoutMock));

        $themeMock->expects($this->once())->method('getId')->will($this->returnValue(1));

        $method = new \ReflectionMethod($listAbstractBlock, '_addAssignButtonHtml');
        $method->setAccessible(true);
        $method->invoke($listAbstractBlock, $themeBlockMock);
    }
}
