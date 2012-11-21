<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Backend
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Mage_Backend_Block_Widget_Button
 */
class Mage_Backend_Block_Widget_ButtonTest extends PHPUnit_Framework_TestCase
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

    protected function setUp()
    {
        $this->_helperMock =
            $this->getMock('Mage_Backend_Helper_Data', array(), array(), '', false);

        $this->_layoutMock =
            $this->getMock('Mage_Core_Model_Layout', array(), array(), '', false);
        $this->_layoutMock
            ->expects($this->any())
            ->method('helper')
            ->will($this->returnValue($this->_helperMock));

        $arguments = array(
            'urlBuilder' =>
                $this->getMock('Mage_Backend_Model_Url', array(), array(), '', false),
            'layout' => $this->_layoutMock
        );

        $objectManagerHelper = new Magento_Test_Helper_ObjectManager($this);
        $this->_blockMock =
            $objectManagerHelper->getBlock('Mage_Backend_Block_Widget_Button', $arguments);
    }

    public function tearDown()
    {
        unset($this->_layoutMock);
        unset($this->_helperMock);
        unset($this->_buttonMock);
    }

    /**
     * @covers Mage_Backend_Block_Widget_Button::getAttributesHtml
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
                    'data_attr' => array(
                        'widget-button' => array('someKey' => 'someValue'),
                    ),
                ),
                '/data-widget-button="[^"]*" /'
            ),
            array(
                array(
                    'data_attr' => array(
                        'mage-init' => array('someKey' => 'someValue'),
                    ),
                ),
                '/data-mage-init="[^"]*" /'
            ),
            array(
                array(
                    'data_attr' => array(
                        'mage-init' => array('someKey' => 'someValue'),
                        'widget-button' => array('someKey' => 'someValue'),
                    ),
                ),
                '/data-mage-init="[^"]*" data-widget-button="[^"]*" /'
            ),
        );
    }
}
