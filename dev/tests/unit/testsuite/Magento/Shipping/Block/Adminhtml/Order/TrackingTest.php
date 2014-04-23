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

        $shipment = new \Magento\Framework\Object(array('store_id' => 1));

        $registry = $this->getMock('Magento\Framework\Registry', array('registry'), array(), '', false);
        $registry->expects(
            $this->once()
        )->method(
            'registry'
        )->with(
            'current_shipment'
        )->will(
            $this->returnValue($shipment)
        );

        $carrier = $this->getMock(
            'Magento\OfflineShipping\Model\Carrier\Freeshipping',
            array('isTrackingAvailable', 'getConfigData'),
            array(),
            '',
            false
        );
        $carrier->expects($this->once())->method('isTrackingAvailable')->will($this->returnValue(true));
        $carrier->expects(
            $this->once()
        )->method(
            'getConfigData'
        )->with(
            'title'
        )->will(
            $this->returnValue('configdata')
        );

        $config = $this->getMock('Magento\Shipping\Model\Config', array('getAllCarriers'), array(), '', false);
        $config->expects(
            $this->once()
        )->method(
            'getAllCarriers'
        )->with(
            1
        )->will(
            $this->returnValue(array('free' => $carrier))
        );

        /** @var \Magento\Shipping\Block\Adminhtml\Order\Tracking $model */
        $model = $helper->getObject(
            'Magento\Shipping\Block\Adminhtml\Order\Tracking',
            array('registry' => $registry, 'shippingConfig' => $config)
        );

        $this->assertEquals(array('custom' => 'Custom Value', 'free' => 'configdata'), $model->getCarriers());
    }
}
