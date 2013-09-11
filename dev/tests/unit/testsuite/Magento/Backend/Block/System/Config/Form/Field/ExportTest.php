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

class Magento_Backend_Block_System_Config_Form_Field_ExportTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Backend\Block\System\Config\Form\Field\Export
     */
    protected $_object;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_helperFactoryMock;

    protected function setUp()
    {
        $this->_helperFactoryMock = $this->getMock('Magento\Core\Model\Factory\Helper',
            array(), array(), '', false, false
        );

        $data = array(
            'helperFactory' => $this->_helperFactoryMock
        );
        $this->_object = new \Magento\Backend\Block\System\Config\Form\Field\Export($data);
    }

    public function testGetElementHtml()
    {
        $expected = 'some test data';

        $form = $this->getMock('Magento\Data\Form', array('getParent'), array(), '', false, false);
        $parentObjectMock = $this->getMock('Magento\Backend\Block\Template',
            array('getLayout'), array(), '', false, false
        );
        $layoutMock = $this->getMock('Magento\Core\Model\Layout', array(), array(), '', false, false);

        $blockMock = $this->getMock('Magento\Backend\Block\Widget\Button', array(), array(), '', false, false);

        $requestMock = $this->getMock('Magento\Core\Controller\Request\Http', array(), array(), '', false, false);
        $requestMock->expects($this->once())->method('getParam')->with('website')->will($this->returnValue(1));

        $helperMock = $this->getMock('Magento\Backend\Helper\Data', array(), array(), '', false, false);
        $helperMock->expects($this->once())->method('getUrl')->with("*/*/exportTablerates", array('website' => 1));

        $this->_helperFactoryMock->expects($this->any())
            ->method('get')->with('Magento\Backend\Helper\Data')->will($this->returnValue($helperMock));

        $mockData = $this->getMock('StdClass', array('toHtml'));
        $mockData->expects($this->once())->method('toHtml')->will($this->returnValue($expected));

        $blockMock->expects($this->once())->method('getRequest')->will($this->returnValue($requestMock));
        $blockMock->expects($this->any())->method('setData')->will($this->returnValue($mockData));


        $layoutMock->expects($this->once())->method('createBlock')->will($this->returnValue($blockMock));
        $parentObjectMock->expects($this->once())->method('getLayout')->will($this->returnValue($layoutMock));
        $form->expects($this->once())->method('getParent')->will($this->returnValue($parentObjectMock));

        $this->_object->setForm($form);
        $this->assertEquals($expected, $this->_object->getElementHtml());
    }
}
