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
        $this->_pdfConfigMock = $this->getMockBuilder('Magento\Sales\Model\Order\Pdf\Config')
            ->disableOriginalConstructor()
            ->getMock();
        $directoryMock = $this->getMock(
            'Magento\Framework\Filesystem\Directory\Write',
            array(),
            array(),
            '',
            false,
            false
        );
        $directoryMock->expects($this->any())->method('getAbsolutePath')->will(
            $this->returnCallback(
                function ($argument) {
                    return BP . '/' . $argument;
                }
            )
        );
        $filesystemMock = $this->getMock('Magento\Framework\Filesystem', array(), array(), '', false, false);
        $filesystemMock->expects($this->any())->method('getDirectoryRead')->will($this->returnValue($directoryMock));
        $filesystemMock->expects($this->any())->method('getDirectoryWrite')->will($this->returnValue($directoryMock));

        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->_model = $helper->getObject(
            'Magento\Sales\Model\Order\Pdf\Invoice',
            [
                'filesystem' => $filesystemMock,
                'pdfConfig' => $this->_pdfConfigMock,
            ]
        );
    }

    public function testGetPdfInitRenderer()
    {
        $this->_pdfConfigMock->expects(
            $this->once()
        )->method(
            'getRenderersPerProduct'
        )->with(
            'invoice'
        )->will(
            $this->returnValue(
                array(
                    'product_type_one' => 'Renderer_Type_One_Product_One',
                    'product_type_two' => 'Renderer_Type_One_Product_Two'
                )
            )
        );

        $this->_model->getPdf(array());
        $renderers = new \ReflectionProperty($this->_model, '_renderers');
        $renderers->setAccessible(true);
        $this->assertSame(
            array(
                'product_type_one' => array('model' => 'Renderer_Type_One_Product_One', 'renderer' => null),
                'product_type_two' => array('model' => 'Renderer_Type_One_Product_Two', 'renderer' => null)
            ),
            $renderers->getValue($this->_model)
        );
    }
}
