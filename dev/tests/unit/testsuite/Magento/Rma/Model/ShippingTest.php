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

class Magento_Rma_Model_ShippingTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Rma_Model_Shipping
     */
    protected $_model;

    protected function setUp()
    {
        $objectManagerHelper = new Magento_TestFramework_Helper_ObjectManager($this);

        $orderFactory = $this->getMock('Magento_Sales_Model_OrderFactory', array('create'), array(), '', false);
        $regionFactory = $this->getMock('Magento_Directory_Model_RegionFactory', array('create'), array(), '', false);
        $returnFactory = $this->getMock('Magento_Shipping_Model_Shipment_ReturnFactory',
            array('create'), array(), '', false);
        $rmaFactory = $this->getMock('Magento_Rma_Model_RmaFactory', array('create'), array(), '', false);

        $this->_model = $objectManagerHelper->getObject('Magento_Rma_Model_Shipping', array(
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
            array(true, Magento_Sales_Model_Order_Shipment_Track::CUSTOM_CARRIER_CODE),
            array(false, 'ups'),
        );
    }
}
