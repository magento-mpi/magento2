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
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_shortcutButtonsMock;

    protected function setUp()
    {
        $this->_observer = new \Magento\Event\Observer();

        $layoutMock = $this->getMockBuilder('Magento\Core\Model\Layout')
            ->setMethods(array('createBlock'))
            ->disableOriginalConstructor()
            ->getMock();
        $layoutMock->expects($this->any())
            ->method('createBlock')
            ->will($this->returnCallback(array($this, 'createBlock')));

        $this->_shortcutButtonsMock = $this->getMockBuilder('Magento\Catalog\Block\ShortcutButtons')
            ->setMethods(array('getLayout', 'addShortcut'))
            ->disableOriginalConstructor()
            ->getMock();
        $this->_shortcutButtonsMock->expects($this->any())
            ->method('getLayout')
            ->will($this->returnValue($layoutMock));

        $event = new \Magento\Object();
        $event->setContainer($this->_shortcutButtonsMock);

        $this->_observer = new \Magento\Event\Observer();
        $this->_observer->setEvent($event);

        $objectManagerHelper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->_model = $objectManagerHelper->getObject('Magento\Paypal\Model\Observer');
    }

    public function testAddPaypalShortcuts()
    {
        $this->_shortcutButtonsMock->expects($this->any())
            ->method('addShortcut')
            ->with($this->callback(
                function($shortcutObj) {
                    return $shortcutObj instanceof \Magento\View\Element\Template\;
                }
            ));
        $this->_model->addPaypalShortcuts($this->_observer);
    }

    /**
     * Emulates Layout createBlock function for Paypal Observer
     *
     * @param string $block
     * @param string $name
     * @param array $args
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    public function createBlock($block, $name, $args)
    {
        $block = $this->getMockBuilder($block)
            ->setMethods(null)
            ->disableOriginalConstructor()
            ->getMock();
        return $block;
    }
}
 