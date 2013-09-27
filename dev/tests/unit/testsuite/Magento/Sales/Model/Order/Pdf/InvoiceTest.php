<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Sales_Model_Order_Pdf_InvoiceTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Sales_Model_Order_Pdf_Invoice
     */
    protected $_model;

    /**
     * @var Magento_Sales_Model_Order_Pdf_Config|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_pdfConfigMock;

    protected function setUp()
    {
        $paymentDataMock = $this->getMock('Magento_Payment_Helper_Data', array(), array(), '', false);
        $coreHelperMock = $this->getMock('Magento_Core_Helper_Data', array(), array(), '', false);
        $coreHelperStringMock = $this->getMock('Magento_Core_Helper_String', array(), array(), '', false, false);
        $storeConfigMock = $this->getMock('Magento_Core_Model_Store_Config', array(), array(), '', false, false);
        $translateMock = $this->getMock('Magento_Core_Model_Translate', array(), array(), '', false, false);
        $coreDirMock = $this->getMock('Magento_Core_Model_Dir', array(), array(), '', false, false);
        $coreDirMock->expects($this->once())
            ->method('getDir')
            ->will($this->returnValue(BP));
        $shippingConfigMock = $this->getMock('Magento_Shipping_Model_Config', array(), array(), '', false,
            false);
        $this->_pdfConfigMock =
            $this->getMock('Magento_Sales_Model_Order_Pdf_Config', array(), array(), '', false, false);
        $totalFactoryMock = $this->getMock('Magento_Sales_Model_Order_Pdf_Total_Factory', array(), array(), '', false,
            false);
        $pdfItemsFactoryMock = $this->getMock('Magento_Sales_Model_Order_Pdf_ItemsFactory', array(), array(), '', false,
            false);
        $localeMock = $this->getMock('Magento_Core_Model_LocaleInterface', array(), array(), '', false,
            false);
        $storeManagerMock = $this->getMock('Magento_Core_Model_StoreManagerInterface', array(), array(), '', false,
            false);

        $this->_model = new Magento_Sales_Model_Order_Pdf_Invoice(
            $paymentDataMock,
            $coreHelperMock,
            $coreHelperStringMock,
            $storeConfigMock,
            $translateMock,
            $coreDirMock,
            $shippingConfigMock,
            $this->_pdfConfigMock,
            $totalFactoryMock,
            $pdfItemsFactoryMock,
            $localeMock,
            $storeManagerMock,
            array()
        );
    }

    public function testGetPdfInitRenderer()
    {
        $this->_pdfConfigMock
            ->expects($this->once())
            ->method('getRenderersPerProduct')
            ->with('invoice')
            ->will($this->returnValue(
                    array(
                        'product_type_one' => 'Renderer_Type_One_Product_One',
                        'product_type_two' => 'Renderer_Type_One_Product_Two',
                        )
                ));

        $this->_model->getPdf(array());
        $renderers = new ReflectionProperty($this->_model, '_renderers');
        $renderers->setAccessible(true);
        $this->assertSame(
            array(
                'product_type_one' => array(
                    'model' => 'Renderer_Type_One_Product_One',
                    'renderer' => null,
                ),
                'product_type_two' => array(
                    'model' => 'Renderer_Type_One_Product_Two',
                    'renderer' => null,
                ),
            ),
            $renderers->getValue($this->_model)
        );
    }
}
