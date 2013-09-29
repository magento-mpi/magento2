<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Checkout\Block\Cart;

class SidebarTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\TestFramework\Helper\ObjectManager */
    protected $_objectManager;

    protected function setUp()
    {
        $this->_objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
    }

    public function testDeserializeRenders()
    {
        $childBlock = $this->getMock('Magento\Core\Block\AbstractBlock', array(), array(), '', false);
        /** @var $layout \Magento\Core\Model\Layout */
        $layout = $this->getMock('Magento\Core\Model\Layout', array(
            'createBlock', 'getChildName', 'setChild'
        ), array(), '', false);
        $layout->expects($this->once())
            ->method('createBlock')
            ->with(
                'some-block',
                '.some-template',
                array('data' => array('template' => 'some-type'))
            )
            ->will($this->returnValue($childBlock));
        $layout->expects($this->any())
            ->method('getChildName')
            ->with(null, 'some-template')
            ->will($this->returnValue(false));
        $layout->expects($this->once())
            ->method('setChild')
            ->with(null, null, 'some-template');

        /** @var $block \Magento\Checkout\Block\Cart\Sidebar */
        $block = $this->_objectManager->getObject('Magento\Checkout\Block\Cart\Sidebar', array(
            'context' => $this->_objectManager->getObject('Magento\Backend\Block\Template\Context', array(
                'layout' => $layout,
            ))
        ));

        $block->deserializeRenders('some-template|some-block|some-type');
    }
}
