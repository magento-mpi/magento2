<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Paypal\Block\Express;

class ShortcutTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Paypal\Block\Express\Shortcut|PHPUnit_Framework_MockObject_MockObject
     */
    protected $model;

    protected function setUp()
    {
        $context = $this->getMock('Magento\View\Element\Template\Context', [], [], '', false);
        $paypalData = $this->getMock('Magento\Paypal\Helper\Data', [], [], '', false);
        $paymentData = $this->getMock('Magento\Payment\Helper\Data', [], [], '', false);
        $registry = $this->getMock('Magento\Core\Model\Registry', [], [], '', false);
        $customerSession = $this->getMock('Magento\Customer\Model\Session', [], [], '', false);
        $paypalConfigFactory = $this->getMock('Magento\Paypal\Model\ConfigFactory', [], [], '', false);
        $checkoutFactory = $this->getMock('Magento\Paypal\Model\Express\Checkout\Factory', [], [], '', false);
        $mathRandom = $this->getMock('Magento\Math\Random', [], [], '', false);
        $productTypeConfig = $this->getMock('Magento\Catalog\Model\ProductTypes\ConfigInterface', [], [], '', false);
        $checkoutSession = $this->getMock('Magento\Checkout\Model\Session', [], [], '', false);

        $this->model = new \Magento\Paypal\Block\Express\Shortcut(
            $context, $paypalData, $paymentData, $registry, $customerSession, $paypalConfigFactory,
            $checkoutFactory, $mathRandom, $productTypeConfig, $checkoutSession, []
        );
    }

    public function testGetAlias()
    {
        $this->assertEquals('product.info.addtocart.paypal', $this->model->getAlias());
    }
}
