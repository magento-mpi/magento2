<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Paypal\Helper\Shortcut;

class ValidatorTest extends \PHPUnit_Framework_TestCase
{
    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $_paypalConfigFactory;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $_registry;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $_productTypeConfig;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $_paymentData;

    /** @var \Magento\Paypal\Helper\Shortcut\Validator */
    protected $helper;

    protected function setUp()
    {
        $this->_paypalConfigFactory = $this->getMock('\Magento\Paypal\Model\ConfigFactory', ['create'], [], '', false);
        $this->_productTypeConfig = $this->getMock(
            'Magento\Catalog\Model\ProductTypes\ConfigInterface',
            [],
            [],
            '',
            false
        );
        $this->_registry = $this->getMock('Magento\Framework\Registry', [], [], '', false);
        $this->_paymentData = $this->getMock('Magento\Payment\Helper\Data', [], [], '', false);

        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->helper = $objectManager->getObject(
            'Magento\Paypal\Helper\Shortcut\Validator',
            array(
                'paypalConfigFactory' => $this->_paypalConfigFactory,
                'registry' => $this->_registry,
                'productTypeConfig' => $this->_productTypeConfig,
                'paymentData' => $this->_paymentData
            )
        );
    }

    /**
     * @dataProvider testIsContextAvailableDataProvider
     * @param bool $isVisible
     * @param bool $expected
     */
    public function testIsContextAvailable($isVisible, $expected)
    {
        $paypalConfig = $this->getMock('PaypalConfig', ['setMethod', 'getConfigValue']);
        $paypalConfig->expects($this->any())
            ->method('getConfigValue')
            ->with($this->stringContains('visible_on'))
            ->will($this->returnValue($isVisible));

        $this->_paypalConfigFactory->expects($this->any())
            ->method('create')
            ->will($this->returnValue($paypalConfig));

        $this->assertEquals($expected, $this->helper->isContextAvailable('payment_code', true));
    }

    /**
     * @return array
     */
    public function testIsContextAvailableDataProvider()
    {
        return [
            [false, false],
            [true, true]
        ];
    }

    /**
     * @dataProvider testIsPriceOrSetAvailableDataProvider
     * @param bool $isInCatalog
     * @param double $productPrice
     * @param bool $isProductSet
     * @param bool $expected
     */
    public function testIsPriceOrSetAvailable($isInCatalog, $productPrice, $isProductSet, $expected)
    {
        $currentProduct = new \Magento\Framework\Object(['final_price' => $productPrice, 'type_id' => 'simple']);
        $this->_registry->expects($this->any())
            ->method('registry')
            ->with($this->equalTo('current_product'))
            ->will($this->returnValue($currentProduct));

        $this->_productTypeConfig->expects($this->any())
            ->method('isProductSet')
            ->will($this->returnValue($isProductSet));

        $this->assertEquals($expected, $this->helper->isPriceOrSetAvailable($isInCatalog));
    }

    /**
     * @return array
     */
    public function testIsPriceOrSetAvailableDataProvider()
    {
        return [
            [false, 1, true, true],
            [false, null, null, true],
            [true, 0, false, false],
            [true, 10, false, true],
            [true, 0, true, true]
        ];
    }

    /**
     * @dataProvider testIsMethodAvailableDataProvider
     * @param bool $methodExists
     * @param bool $methodIsAvailable
     * @param bool $expected
     */
    public function testIsMethodAvailable($methodExists, $methodIsAvailable, $expected)
    {
        $methodInstance = $this->getMock('MethodInstance', ['isAvailable']);
        $methodInstance->expects($this->any())
            ->method('isAvailable')
            ->will($this->returnValue($methodIsAvailable));

        $this->_paymentData->expects($this->any())
            ->method('getMethodInstance')
            ->will(
                $this->returnValue($methodExists ? $methodInstance : false)
            );

        $this->assertEquals($expected, $this->helper->isMethodAvailable('payment_code'));
    }

    /**
     * @return array
     */
    public function testIsMethodAvailableDataProvider()
    {
        return [
            [false, true, false],
            [true, false, false],
            [false, false, false],
            [true, true, true]
        ];
    }
}
