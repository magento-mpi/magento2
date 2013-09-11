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
class Magento_Backend_Block_Widget_ButtonTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_layoutMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_helperMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_factoryMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_blockMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_buttonMock;

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

        $arguments = array(
            'urlBuilder' =>
                $this->getMock('Magento\Backend\Model\Url', array(), array(), '', false, false),
            'layout' => $this->_layoutMock
        );

        $objectManagerHelper = new Magento_TestFramework_Helper_ObjectManager($this);
        $this->_blockMock =
            $objectManagerHelper->getObject('\Magento\Backend\Block\Widget\Button', $arguments);
    }

    public function tearDown()
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
