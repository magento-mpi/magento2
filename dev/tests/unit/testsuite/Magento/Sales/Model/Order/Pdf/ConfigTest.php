<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Sales_Model_Order_Pdf_ConfigTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Sales_Model_Order_Pdf_Config
     */
    protected $_model;

    /**
     * @var Magento_Config_Data|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_dataStorage;

    protected function setUp()
    {
        $this->_dataStorage = $this->getMock('Magento_Config_Data', array(), array(), '', false);
        $this->_model = new Magento_Sales_Model_Order_Pdf_Config($this->_dataStorage);
    }

    public function testGetRenderersPerProduct()
    {
        $configuration = array(
            'product_type_one' => 'Renderer_One',
            'product_type_two' => 'Renderer_Two',
        );
        $this->_dataStorage
            ->expects($this->once())
            ->method('get')
            ->with("renderers/page_type", array())
            ->will($this->returnValue($configuration));

        $this->assertSame($configuration, $this->_model->getRenderersPerProduct('page_type'));
    }

    public function testGetTotals()
    {
        $configuration = array(
            'total1' => array('title' => 'Title1'),
            'total2' => array('title' => 'Title2'),
        );

        $this->_dataStorage
            ->expects($this->once())
            ->method('get')
            ->with('totals', array())
            ->will($this->returnValue($configuration));

        $this->assertSame($configuration, $this->_model->getTotals());
    }
}
