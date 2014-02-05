<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Paypal\Model;

class ObserverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Paypal\Model\Observer
     */
    protected $_model;

    /**
     * @var \Magento\Event\Observer
     */
    protected $_observer;

    /**
     * @var \Magento\Object
     */
    protected $_event;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_shortcutButtonsMock;

    protected function setUp()
    {
        $this->_event = new \Magento\Object();

        $this->_observer = new \Magento\Event\Observer();
        $this->_observer->setEvent($this->_event);

        $objectManagerHelper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->_model = $objectManagerHelper->getObject('Magento\Paypal\Model\Observer');
    }

    public function testAddPaypalShortcuts()
    {
        $layoutMock = $this->getMockBuilder('Magento\Core\Model\Layout')
            ->setMethods(array('createBlock'))
            ->disableOriginalConstructor()
            ->getMock();
        $blocks = array(
            'Magento\Paypal\Block\Express\Shortcut',
            'Magento\Paypal\Block\PayflowExpress\Shortcut'
        );

        foreach ($blocks as $atPosition => $blockName) {
            $block = $this->getMockBuilder($blockName)
                ->setMethods(null)
                ->disableOriginalConstructor()
                ->getMock();

            $layoutMock->expects($this->at($atPosition))
                ->method('createBlock')
                ->with($blockName)
                ->will($this->returnValue($block));
        }

        $shortcutButtonsMock = $this->getMockBuilder('Magento\Catalog\Block\ShortcutButtons')
            ->setMethods(array('getLayout', 'addShortcut'))
            ->disableOriginalConstructor()
            ->getMock();

        $shortcutButtonsMock->expects($this->at(0))
            ->method('getLayout')
            ->will($this->returnValue($layoutMock));
        $shortcutButtonsMock->expects($this->at(1))
            ->method('addShortcut')
            ->with($this->isInstanceOf('Magento\Paypal\Block\Express\Shortcut'));

        $shortcutButtonsMock->expects($this->at(2))
            ->method('getLayout')
            ->will($this->returnValue($layoutMock));
        $shortcutButtonsMock->expects($this->at(3))
            ->method('addShortcut')
            ->with($this->isInstanceOf('Magento\Paypal\Block\PayflowExpress\Shortcut'));


        $this->_event->setContainer($shortcutButtonsMock);
        $this->_model->addPaypalShortcuts($this->_observer);
    }

}
 