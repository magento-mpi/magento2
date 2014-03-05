<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Shipping\Block\Adminhtml\Order;

class TrackingTest extends \PHPUnit_Framework_TestCase
{
    public function testLookup()
    {
        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);

        $shipment = new \Magento\Object(['store_id' => 1]);

        $registry = $this->getMock('Magento\Registry', ['registry'], [], '', false);
        $registry->expects($this->once())->method('registry')
            ->with('current_shipment')->will($this->returnValue($shipment));

        $carrier = $this->getMock('Magento\OfflineShipping\Model\Carrier\Freeshipping',
            ['isTrackingAvailable', 'getConfigData'], [], '', false);
        $carrier->expects($this->once())->method('isTrackingAvailable')->will($this->returnValue(true));
        $carrier->expects($this->once())->method('getConfigData')->with('title')
            ->will($this->returnValue('configdata'));

        $config = $this->getMock('Magento\Shipping\Model\Config', ['getAllCarriers'], [], '', false);
        $config->expects($this->once())->method('getAllCarriers')
            ->with(1)->will($this->returnValue(['free' => $carrier]));

        /** @var \Magento\Shipping\Block\Adminhtml\Order\Tracking $model */
        $model = $helper->getObject(
            'Magento\Shipping\Block\Adminhtml\Order\Tracking',
            [
                'registry' => $registry,
                'shippingConfig' => $config
            ]
        );

        $this->assertEquals([
            'custom' => 'Custom Value',
            'free' => 'configdata'
        ], $model->getCarriers());
    }
}
