<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for \Magento\Backend\Block\Widget\Button
 */
namespace Magento\Backend\Block\Widget;

class ButtonTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_layoutMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_helperMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_factoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_blockMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_buttonMock;

    /**
     * @var \Magento\Core\Model\Factory\Helper
     */
    protected $_helperFactoryMock;

    protected function setUp()
    {
        $this->_helperMock =
            $this->getMock('Magento\Backend\Helper\Data', array('uniqHash'), array(), '', false, false);

        $this->_layoutMock =
            $this->getMock('Magento\Core\Model\Layout', array(), array(), '', false, false);
        $this->_layoutMock
            ->expects($this->any())
            ->method('helper')
            ->will($this->returnValue($this->_helperMock));

        $this->_helperFactoryMock = $this->getMock(
            'Magento\Core\Model\Factory\Helper', array('get'), array(), '', false, false
        );

        $arguments = array(
            'urlBuilder' =>
                $this->getMock('Magento\Backend\Model\Url', array(), array(), '', false, false),
            'layout' => $this->_layoutMock,
            'helperFactory' => $this->_helperFactoryMock,
        );

        $objectManagerHelper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->_blockMock =
            $objectManagerHelper->getObject('Magento\Backend\Block\Widget\Button', $arguments);
    }

    protected function tearDown()
    {
        unset($this->_layoutMock);
        unset($this->_helperMock);
        unset($this->_buttonMock);
    }

    /**
     * @covers \Magento\Backend\Block\Widget\Button::getAttributesHtml
     * @dataProvider getAttributesHtmlDataProvider
     */
    public function testGetAttributesHtml($data, $expect)
    {
        $coreHelperMock = $this->getMock(
            'Magento\Core\Helper\Data', array(), array(), '', false, false
        );
        $backendHelperMock = $this->getMock(
            'Magento\Backend\Helper\Data', array(), array(), '', false, false
        );
        $this->_helperFactoryMock
            ->expects($this->any())
            ->method('get')
            ->will($this->returnValueMap(array(
                array('Magento\Core\Helper\Data', array(), $coreHelperMock),
                array('Magento\Backend\Helper\Data', array(), $backendHelperMock),
            )));

        $this->_blockMock->setData($data);
        $attributes = $this->_blockMock->getAttributesHtml();
        $this->assertRegExp($expect, $attributes);
    }

    public function getAttributesHtmlDataProvider()
    {
        return array(
            array(
                array(
                    'data_attribute' => array(
                        'validation' => array(
                            'required' => true
                        ),
                    ),
                ),
                '/data-validation="[^"]*" /'
            ),
            array(
                array(
                    'data_attribute' => array(
                        'mage-init' => array(
                            'button' => array('someKey' => 'someValue')
                        ),
                    ),
                ),
                '/data-mage-init="[^"]*" /'
            ),
            array(
                array(
                    'data_attribute' => array(
                        'mage-init' => array(
                            'button' => array('someKey' => 'someValue')
                        ),
                        'validation' => array('required' => true),
                    ),
                ),
                '/data-mage-init="[^"]*" data-validation="[^"]*" /'
            ),
        );
    }
}
