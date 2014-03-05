<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Usa\Helper;


class DataTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Usa\Helper\Data
     */
    protected $_helperData;

    /**
     *
     */
    protected function setUp()
    {
        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $arguments = array(
            'context' => $this->getMock('Magento\App\Helper\Context', array(), array(), '', false),
            'locale' => $this->getMock('Magento\Core\Model\Locale', array(), array(), '', false)
        );

        $this->_helperData = $helper->getObject('Magento\Usa\Helper\Data', $arguments);
    }

    /**
     * @covers \Magento\Usa\Helper\Data::displayGirthValue
     * @dataProvider shippingMethodDataProvider
     */
    public function testDisplayGirthValue($shippingMethod)
    {
        $this->assertTrue($this->_helperData->displayGirthValue($shippingMethod));
    }

    /**
     * @covers \Magento\Usa\Helper\Data::displayGirthValue
     */
    public function testDisplayGirthValueFalse()
    {
        $this->assertFalse($this->_helperData->displayGirthValue('test_shipping_method'));
    }

    /**
     * @return array shipping method name
     */
    public function shippingMethodDataProvider()
    {
        return array(
            array('usps_0_FCLE'), // First-Class Mail Large Envelope
            array('usps_1'),      // Priority Mail
            array('usps_2'),      // Priority Mail Express Hold For Pickup
            array('usps_3'),      // Priority Mail Express
            array('usps_4'),      // Standard Post
            array('usps_6'),      // Media Mail
            array('usps_INT_1'),  // Priority Mail Express International
            array('usps_INT_2'),  // Priority Mail International
            array('usps_INT_4'),  // Global Express Guaranteed (GXG)
            array('usps_INT_7'),  // Global Express Guaranteed Non-Document Non-Rectangular
            array('usps_INT_8'),  // Priority Mail International Flat Rate Envelope
            array('usps_INT_9'),  // Priority Mail International Medium Flat Rate Box
            array('usps_INT_10'), // Priority Mail Express International Flat Rate Envelope
            array('usps_INT_11'), // Priority Mail International Large Flat Rate Box
            array('usps_INT_12'), // USPS GXG Envelopes
            array('usps_INT_14'), // First-Class Mail International Large Envelope
            array('usps_INT_16'), // Priority Mail International Small Flat Rate Box
            array('usps_INT_20'), // Priority Mail International Small Flat Rate Envelope
            array('usps_INT_26'), // Priority Mail Express International Flat Rate Boxes
        );
    }
}
