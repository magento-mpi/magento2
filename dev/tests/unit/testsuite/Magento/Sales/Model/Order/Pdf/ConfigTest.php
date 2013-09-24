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
     * @var Magento_Sales_Model_Order_Pdf_Config_Data|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_dataStorage;

    protected function setUp()
    {
        $this->_dataStorage = $this->getMock('Magento_Sales_Model_Order_Pdf_Config_Data', array(), array(), '', false);
        $this->_model = new Magento_Sales_Model_Order_Pdf_Config($this->_dataStorage);
    }

    /**
     * @param $pageType
     * @param $expectedResult
     * @dataProvider getRendererDataDataProvider
     */
    public function testGetRendererData($pageType, $expectedResult)
    {
        $dataStorage = require __DIR__ . '/Config/_files/pdf_merged.php';
        $returnValue = isset($dataStorage['renderers'][$pageType]) ? $dataStorage['renderers'][$pageType] : array();
        $this->_dataStorage
            ->expects($this->once())
            ->method('get')
            ->with("renderers/{$pageType}", array())
            ->will($this->returnValue($returnValue));

        $this->assertSame($expectedResult, $this->_model->getRendererData($pageType));
    }

    /**
     * @return array
     */
    public function getRendererDataDataProvider()
    {
        return array(
            'page type exists' => array(
                'type_one',
                array(
                    'product_type_one' => 'Renderer_Type_One_Product_One',
                    'product_type_two' => 'Renderer_Type_One_Product_Two'
                )
            ),
            'page type does not exist' => array(
                'wrong_type',
                array()
            ),
        );
    }

    public function testGetTotals()
    {
        $dataStorage = require __DIR__ . '/Config/_files/pdf_merged.php';
        $this->_dataStorage
            ->expects($this->once())
            ->method('get')
            ->with('totals')
            ->will($this->returnValue($dataStorage['totals']));

        $this->assertSame($dataStorage['totals'], $this->_model->getTotals());
    }
}
