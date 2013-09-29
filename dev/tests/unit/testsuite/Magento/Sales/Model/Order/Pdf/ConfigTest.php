<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Model\Order\Pdf;

class ConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Sales\Model\Order\Pdf\Config
     */
    protected $_model;

    /**
     * @var \Magento\Config\Data|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_dataStorage;

    protected function setUp()
    {
        $this->_dataStorage = $this->getMock('Magento\Config\Data', array(), array(), '', false);
        $this->_model = new \Magento\Sales\Model\Order\Pdf\Config($this->_dataStorage);
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
