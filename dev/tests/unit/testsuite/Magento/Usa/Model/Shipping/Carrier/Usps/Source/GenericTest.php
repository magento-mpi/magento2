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
    public function _getGenericInstance($options)
    {
        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $mockUsps = $this->getMockBuilder('Magento\Usa\Model\Shipping\Carrier\Usps')
            ->setMethods(array('getCode'))
            ->disableOriginalConstructor()
            ->getMock();

        $mockUsps->expects($this->once())
            ->method('getCode')
            ->will($this->returnValue($options));

        return $helper->getObject(
            'Magento\Usa\Model\Shipping\Carrier\Usps\Source\Generic',
            array('shippingUsps' => $mockUsps)
        );
    }

    public function testToOptionArray()
    {
        $expected = array(
            array(
            'value' => 'Val',
            'label' => 'Label'
            )
        );
        $genericModel = $this->_getGenericInstance(array('Val'=>'Label'));
        $this->assertEquals($expected, $genericModel->toOptionArray());
    }

    public function testToOptionArrayBool()
    {
        $genericModel = $this->_getGenericInstance(false);
        $this->assertEmpty($genericModel->toOptionArray());
    }

}
