<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Usps\Helper;

class DataTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Usps\Helper\Data
     */
    protected $_helperData;

    protected function setUp()
    {
        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $arguments = array(
            'context' => $this->getMock('Magento\App\Helper\Context', array(), array(), '', false),
            'locale' => $this->getMock('Magento\Locale', array(), array(), '', false)
        );

        $this->_helperData = $helper->getObject('Magento\Usps\Helper\Data', $arguments);
    }

    /**
     * @covers \Magento\Usps\Helper\Data::displayGirthValue
     * @dataProvider shippingMethodDataProvider
     */
    public function testDisplayGirthValue($shippingMethod)
    {
        $this->assertTrue($this->_helperData->displayGirthValue($shippingMethod));
    }

    /**
     * @covers \Magento\Usps\Helper\Data::displayGirthValue
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
            array('usps_0_FCLE'),
            array('usps_1'),
            array('usps_2'),
            array('usps_3'),
            array('usps_4'),
            array('usps_6'),
            array('usps_INT_1'),
            array('usps_INT_2'),
            array('usps_INT_4'),
            array('usps_INT_7'),
            array('usps_INT_8'),
            array('usps_INT_9'),
            array('usps_INT_10'),
            array('usps_INT_11'),
            array('usps_INT_12'),
            array('usps_INT_14'),
            array('usps_INT_16'),
            array('usps_INT_20'),
            array('usps_INT_26')
        );
    }
}
