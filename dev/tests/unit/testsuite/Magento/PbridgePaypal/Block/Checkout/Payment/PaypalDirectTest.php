<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\PbridgePaypal\Block\Checkout\Payment;

use Magento\TestFramework\Helper\ObjectManager as ObjectManagerHelper;

class PaypalDirectTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\PbridgePaypal\Block\Checkout\Payment\PaypalDirect */
    protected $paypalDirect;

    /** @var ObjectManagerHelper */
    protected $objectManagerHelper;

    protected function setUp()
    {
        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $this->paypalDirect = $this->objectManagerHelper->getObject(
            'Magento\PbridgePaypal\Block\Checkout\Payment\PaypalDirect'
        );
    }

    public function testIs3dSecureEnabled()
    {
        $paymentMethod = $this->getMockBuilder(
            'Magento\Payment\Model\MethodInterface'
        )->disableOriginalConstructor()->setMethods(
            ['getConfigData', 'getCode', 'getFormBlockType', 'getTitle']
        )->getMock();
        $paymentMethod->expects($this->once())->method('getConfigData')->with('centinel')->will(
            $this->returnValue(true)
        );

        $this->paypalDirect->setData('method', $paymentMethod);

        $this->assertTrue($this->paypalDirect->is3dSecureEnabled());
    }
}
