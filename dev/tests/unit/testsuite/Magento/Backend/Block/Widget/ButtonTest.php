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
    protected $_factoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_blockMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_buttonMock;

    protected function setUp()
    {
        $this->_layoutMock =
            $this->getMock('Magento\Core\Model\Layout', array(), array(), '', false, false);

        $arguments = array(
            'urlBuilder' => $this->getMock('Magento\Backend\Model\Url', array(), array(), '', false, false),
            'layout' => $this->_layoutMock,
        );

        $objectManagerHelper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->_blockMock = $objectManagerHelper->getObject('Magento\Backend\Block\Widget\Button', $arguments);
    }

    protected function tearDown()
    {
        unset($this->_layoutMock);
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
