<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\ConfigurableProduct\Block\Adminhtml\Product\Edit\Tab\Super\Config;

class MatrixTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Object under test
     *
     * @var \Magento\ConfigurableProduct\Block\Adminhtml\Product\Edit\Tab\Super\Config\Matrix
     */
    protected $_block;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $_appConfig;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $_locale;

    protected function setUp()
    {
        $this->_appConfig = $this->getMock('Magento\App\ConfigInterface');
        $this->_locale = $this->getMock('Magento\Core\Model\LocaleInterface', array(), array(), '', false);
        $data = array(
            'applicationConfig' => $this->_appConfig,
            'locale' => $this->_locale,
            'formFactory' => $this->getMock('Magento\Data\FormFactory', array(), array(), '', false),
            'productFactory' => $this->getMock('Magento\Catalog\Model\ProductFactory', array(), array(), '', false),
        );
        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->_object = $helper->getObject('Magento\Backend\Block\System\Config\Form', $data);
        $this->_block = $helper->getObject(
            'Magento\ConfigurableProduct\Block\Adminhtml\Product\Edit\Tab\Super\Config\Matrix', $data
        );
    }

    public function testRenderPrice()
    {
        $this->_appConfig->expects($this->once())
            ->method('getValue')->will($this->returnValue('USD'));
        $currency = $this->getMock('Zend_Currency', array(), array(), '', false);
        $currency->expects($this->once())
            ->method('toCurrency')->with('100.0000')->will($this->returnValue('$100.00'));
        $this->_locale->expects($this->once())
            ->method('currency')->with('USD')->will($this->returnValue($currency));
        $this->assertEquals('$100.00', $this->_block->renderPrice(100));
    }
}
