<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Usps\Model\Source;

class GenericTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Usps\Model\Source\Generic
     */
    protected $_generic;

    /**
     * @var \Magento\Usps\Model\Carrier
     */
    protected $_uspsModel;

    public function setUp()
    {
        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->_uspsModel = $this->getMockBuilder('Magento\Usps\Model\Carrier')
            ->setMethods(array('getCode'))
            ->disableOriginalConstructor()
            ->getMock();

        $this->_generic = $helper->getObject(
            '\Magento\Usps\Model\Source\Generic',
            array('shippingUsps' => $this->_uspsModel)
        );
    }

    /**
     * @dataProvider getCodeDataProvider
     * @param array$expected array
     * @param array $options
     */
    public function testToOptionArray($expected, $options)
    {
        $this->_uspsModel->expects($this->any())
            ->method('getCode')
            ->will($this->returnValue($options));

        $this->assertEquals($expected, $this->_generic->toOptionArray());
    }

    /**
     * @return array expected result and return of \Magento\Usps\Model\Carrier::getCode
     */
    public function getCodeDataProvider()
    {
        return array(
            array(array(array('value' => 'Val', 'label' => 'Label')), array('Val' => 'Label')),
            array(array(), false),
        );
    }
}
