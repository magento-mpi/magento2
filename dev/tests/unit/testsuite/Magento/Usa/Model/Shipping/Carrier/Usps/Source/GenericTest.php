<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Usa\Model\Shipping\Carrier\Usps\Source;

class GenericTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Usa\Model\Shipping\Carrier\Usps\Source\Generic
     */
    protected $_generic;

    /**
     * @var \Magento\Usa\Model\Shipping\Carrier\Usps
     */
    protected $_uspsModel;

    public function setUp()
    {
        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->_uspsModel = $this->getMockBuilder('Magento\Usa\Model\Shipping\Carrier\Usps')
            ->setMethods(array('getCode'))
            ->disableOriginalConstructor()
            ->getMock();

        $this->_generic = $helper->getObject(
            'Magento\Usa\Model\Shipping\Carrier\Usps\Source\Generic',
            array('shippingUsps' => $this->_uspsModel)
        );
    }

    /**
     * @dataProvider getCodeDataProvider
     * @param $expected array
     * @param $options Magento\Usa\Model\Shipping\Carrier\Usps::getCode result
     */
    public function testToOptionArray($expected, $options)
    {
        $this->_uspsModel->expects($this->any())
            ->method('getCode')
            ->will($this->returnValue($options));

        $this->assertEquals($expected, $this->_generic->toOptionArray());
    }

    /**
     * @return array expected result and return of Magento\Usa\Model\Shipping\Carrier\Usps::getCode
     */
    public function getCodeDataProvider()
    {
        return array(
            array(array(array('value' => 'Val', 'label' => 'Label')), array('Val' => 'Label')),
            array(array(), false)
        );
    }
}
