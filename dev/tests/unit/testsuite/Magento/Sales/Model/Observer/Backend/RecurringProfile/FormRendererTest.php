<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

class Magento_Sales_Model_Observer_Backend_RecurringProfile_FormRendererTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Sales_Model_Observer_Backend_RecurringProfile_FormRenderer
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_blockFactoryMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_observerMock;

    protected function setUp()
    {
        $this->_blockFactoryMock = $this->getMock(
            'Magento_Core_Model_BlockFactory', array('createBlock'), array(), '', false
        );
        $this->_observerMock = $this->getMock('Magento_Event_Observer', array(), array(), '', false);
        $this->_model = new Magento_Sales_Model_Observer_Backend_RecurringProfile_FormRenderer(
            $this->_blockFactoryMock
        );
    }

    public function testRender()
    {
        $blockMock = $this->getMock(
            'Magento_Core_Block', array(
                'setNameInLayout', 'setParentElement', 'setProductEntity', 'toHtml', 'addFieldMap',
                'addFieldDependence', 'addConfigOptions'
            )
        );
        $map = array(
            array('Magento_Sales_Block_Adminhtml_Recurring_Profile_Edit_Form', array(), $blockMock),
            array('Magento_Backend_Block_Widget_Form_Element_Dependence', array(), $blockMock)

        );
        $event = $this->getMock(
            'Magento_Event', array('getProductElement', 'getProduct', 'getResult'), array(), '', false
        );
        $this->_observerMock->expects($this->any())->method('getEvent')->will($this->returnValue($event));
        $profileElement = $this->getMock('Magento_Data_Form_Element_Abstract', array(), array(), '', false);
        $event->expects($this->once())->method('getProductElement')->will($this->returnValue($profileElement));
        $product = $this->getMock('Magento_Catalog_Model_Product', array(), array(), '', false);
        $event->expects($this->once())->method('getProduct')->will($this->returnValue($product));
        $this->_blockFactoryMock->expects($this->any())->method('createBlock')->will($this->returnValueMap($map));
        $blockMock->expects($this->any())->method('setNameInLayout');
        $blockMock->expects($this->once())->method('setParentElement')->with($profileElement);
        $blockMock->expects($this->once())->method('setProductEntity')->with($product);
        $blockMock->expects($this->exactly(2))->method('toHtml')->will($this->returnValue('html'));
        $blockMock->expects($this->once())->method('addConfigOptions')->with(array('levels_up' => 2));
        $result = new StdClass();
        $event->expects($this->once())->method('getResult')->will($this->returnValue($result));
        $this->_model->render($this->_observerMock);
        $this->assertEquals('htmlhtml', $result->output);
    }
}
