<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Adminhtml_Block_Catalog_Product_Edit_Tab_Super_Config_MatrixTest extends PHPUnit_Framework_TestCase
{
    /**
     * Object under test
     *
     * @var \Magento\Adminhtml\Block\Catalog\Product\Edit\Tab\Super\Config\Matrix
     */
    protected $_block;

    /** @var \Magento\Backend\Block\Template\Context|PHPUnit_Framework_MockObject_MockObject */
    protected $_context;

    /** @var \Magento\Core\Model\App|PHPUnit_Framework_MockObject_MockObject */
    protected $_application;

    /** @var \Magento\Core\Model\LocaleInterface|PHPUnit_Framework_MockObject_MockObject */
    protected $_locale;

    protected function setUp()
    {
        $this->_context = $this->getMock('Magento\Backend\Block\Template\Context', array(), array(), '', false);
        $this->_application = $this->getMock('Magento\Core\Model\App', array(), array(), '', false);
        $this->_locale = $this->getMock('Magento\Core\Model\LocaleInterface', array(), array(), '', false);
        $this->_block = new \Magento\Adminhtml\Block\Catalog\Product\Edit\Tab\Super\Config\Matrix(
            $this->_context,
            $this->_application,
            $this->_locale
        );
    }

    public function testRenderPrice()
    {
        $this->_application->expects($this->once())
            ->method('getBaseCurrencyCode')->with()->will($this->returnValue('USD'));
        $currency = $this->getMock('Zend_Currency', array(), array(), '', false);
        $currency->expects($this->once())
            ->method('toCurrency')->with('100.0000')->will($this->returnValue('$100.00'));
        $this->_locale->expects($this->once())
            ->method('currency')->with('USD')->will($this->returnValue($currency));
        $this->assertEquals('$100.00', $this->_block->renderPrice(100));
    }
}
