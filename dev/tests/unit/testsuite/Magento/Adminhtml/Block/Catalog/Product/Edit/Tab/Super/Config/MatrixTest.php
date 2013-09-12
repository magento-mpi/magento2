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
     * @var Magento_Adminhtml_Block_Catalog_Product_Edit_Tab_Super_Config_Matrix
     */
    protected $_block;

    /** @var Magento_Backend_Block_Template_Context|PHPUnit_Framework_MockObject_MockObject */
    protected $_context;

    /** @var Magento_Core_Model_App|PHPUnit_Framework_MockObject_MockObject */
    protected $_application;

    /** @var Magento_Core_Model_LocaleInterface|PHPUnit_Framework_MockObject_MockObject */
    protected $_locale;

    protected function setUp()
    {
        $this->_context = $this->getMock('Magento_Backend_Block_Template_Context', array(), array(), '', false);
        $this->_application = $this->getMock('Magento_Core_Model_App', array(), array(), '', false);
        $this->_locale = $this->getMock('Magento_Core_Model_LocaleInterface', array(), array(), '', false);
        $coreRegistry = $this->getMock('Magento_Core_Model_Registry', array(), array(), '', false);
        $this->_block = new Magento_Adminhtml_Block_Catalog_Product_Edit_Tab_Super_Config_Matrix(
            $this->_context,
            $this->_application,
            $this->_locale,
            $coreRegistry
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
