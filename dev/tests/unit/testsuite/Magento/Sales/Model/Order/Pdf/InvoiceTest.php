<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Model\Order\Pdf;

class InvoiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Sales\Model\Order\Pdf\Invoice
     */
    protected $_model;

    /**
     * @var \Magento\Sales\Model\Order\Pdf\Config|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_pdfConfigMock;

    protected function setUp()
    {
        $paymentDataMock = $this->getMock('Magento\Payment\Helper\Data', array(), array(), '', false);
        $coreHelperMock = $this->getMock('Magento\Core\Helper\Data', array(), array(), '', false);
        $coreHelperStringMock = $this->getMock('Magento\Core\Helper\String', array(), array(), '', false, false);
        $storeConfigMock = $this->getMock('Magento\Core\Model\Store\Config', array(), array(), '', false, false);
        $translateMock = $this->getMock('Magento\Core\Model\Translate', array(), array(), '', false, false);
        $coreDirMock = $this->getMock('Magento\Core\Model\Dir', array(), array(), '', false, false);
        $coreDirMock->expects($this->once())
            ->method('getDir')
            ->will($this->returnValue(BP));
        $shippingConfigMock = $this->getMock('Magento\Shipping\Model\Config', array(), array(), '', false,
            false);
        $this->_pdfConfigMock =
            $this->getMock('Magento\Sales\Model\Order\Pdf\Config', array(), array(), '', false, false);
        $totalFactoryMock = $this->getMock('Magento\Sales\Model\Order\Pdf\Total\Factory', array(), array(), '', false,
            false);
        $pdfItemsFactoryMock = $this->getMock('Magento\Sales\Model\Order\Pdf\ItemsFactory', array(), array(), '', false,
            false);
        $localeMock = $this->getMock('Magento\Core\Model\LocaleInterface', array(), array(), '', false,
            false);
        $storeManagerMock = $this->getMock('Magento\Core\Model\StoreManagerInterface', array(), array(), '', false,
            false);

        $this->_model = new \Magento\Sales\Model\Order\Pdf\Invoice(
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
        $renderers = new \ReflectionProperty($this->_model, '_renderers');
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
