<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Multishipping\Model\Payment\Method\Specification;

/**
 * ConfigTest
 */
class ConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Multishipping\Model\Payment\Method\Specification\Config
     */
    protected $configSpecification;

    /** @var  array */
    protected $_targetArray;

    /**
     * Set up
     */
    public function setUp()
    {
        $methodsInfo = array(
            'firstdata' => array(
                'deny_multiple_address' => 1,
            ),
            'sagepay_direct' => array(
                'deny_multiple_address_if3dsecure' => 1,
            ),
            'checkmo' => array(
                'deny_multiple_address' => 0,
            ),
            'authorizenet' => array(
                'deny_multiple_address_if3dsecure' => 0,
            ),
        );
        $config = $this->getMock('\Magento\Payment\Model\Config', array(), array(), '', false);
        $config->expects($this->once())->method('getMethodsInfo')->will($this->returnValue($methodsInfo));
        $this->configSpecification = new \Magento\Multishipping\Model\Payment\Method\Specification\Config($config);
    }

    /**
     * Test isSatisfiedBy method
     *
     * @param string $method
     * @param bool $result
     * @dataProvider methodsDataProvider
     */
    public function testIsSatisfiedBy($method, $result)
    {
        $this->assertEquals(
            $result,
            $this->configSpecification->isSatisfiedBy($method),
            sprintf('Failed payment method test: "%s"', $method)
        );
    }

    /**
     * @return array
     */
    public function methodsDataProvider()
    {
        return array(
            array('firstdata', false),
            array('sagepay_direct', false),
            array('checkmo', true),
            array('authorizenet', true),
        );
    }
}
