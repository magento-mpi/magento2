<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rma
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Rma\Model;

class ShippingTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Rma\Model\Shipping
     */
    protected $_model;

    protected function setUp()
    {
        $objectManagerHelper = new \Magento\TestFramework\Helper\ObjectManager($this);

        $orderFactory = $this->getMock('Magento\Sales\Model\OrderFactory', array('create'), array(), '', false);
        $regionFactory = $this->getMock('Magento\Directory\Model\RegionFactory', array('create'), array(), '', false);
        $returnFactory = $this->getMock('Magento\Shipping\Model\Shipment\ReturnShipmentFactory',
            array('create'), array(), '', false);
        $rmaFactory = $this->getMock('Magento\Rma\Model\RmaFactory', array('create'), array(), '', false);

        $this->_model = $objectManagerHelper->getObject('Magento\Rma\Model\Shipping', array(
            'orderFactory'  => $orderFactory,
            'regionFactory' => $regionFactory,
            'returnFactory' => $returnFactory,
            'rmaFactory'    => $rmaFactory
        ));
    }

    /**
     * @dataProvider isCustomDataProvider
     * @param bool $expectedResult
     * @param string $carrierCodeToSet
     */
    public function testIsCustom($expectedResult, $carrierCodeToSet)
    {
        $this->_model->setCarrierCode($carrierCodeToSet);
        $this->assertEquals($expectedResult, $this->_model->isCustom());
    }

    /**
     * @return array
     */
    public static function isCustomDataProvider()
    {
        return array(
            array(true, \Magento\Sales\Model\Order\Shipment\Track::CUSTOM_CARRIER_CODE),
            array(false, 'ups'),
        );
    }
}
