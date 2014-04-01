<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Block\Widget\Grid\Column\Renderer;

class RadioTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Radio
     */
    protected $_object;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_converter;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_column;

    protected function setUp()
    {
        $context = $this->getMock('\Magento\Backend\Block\Context', array(), array(), '', false);
        $this->_converter = $this->getMock(
            '\Magento\Backend\Block\Widget\Grid\Column\Renderer\Options\Converter',
            array('toFlatArray'),
            array(),
            '',
            false
        );
        $this->_column = $this->getMock(
            'Magento\Backend\Block\Widget\Grid\Column',
            array('getValues', 'getIndex', 'getHtmlName'),
            array(),
            '',
            false
        );
        $this->_object = new Radio($context, $this->_converter);
        $this->_object->setColumn($this->_column);
    }

    /**
     * @param array $rowData
     * @param string $expectedResult
     * @dataProvider renderDataProvider
     */
    public function testRender(array $rowData, $expectedResult)
    {
        $selectedTreeArray = array(array('value' => 1, 'label' => 'One'));
        $selectedFlatArray = array(1 => 'One');
        $this->_column->expects($this->once())->method('getValues')->will($this->returnValue($selectedTreeArray));
        $this->_column->expects($this->once())->method('getIndex')->will($this->returnValue('label'));
        $this->_column->expects($this->once())->method('getHtmlName')->will($this->returnValue('test[]'));
        $this->_converter->expects(
            $this->once()
        )->method(
            'toFlatArray'
        )->with(
            $selectedTreeArray
        )->will(
            $this->returnValue($selectedFlatArray)
        );
        $this->assertEquals($expectedResult, $this->_object->render(new \Magento\Object($rowData)));
    }

    public function renderDataProvider()
    {
        return array(
            'checked' => array(
                array('id' => 1, 'label' => 'One'),
                '<input type="radio" name="test[]" value="1" class="radio" checked="checked"/>'
            ),
            'not checked' => array(
                array('id' => 2, 'label' => 'Two'),
                '<input type="radio" name="test[]" value="2" class="radio"/>'
            )
        );
    }
}
