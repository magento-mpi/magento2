<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Multishipping\Model\Payment\Method\Specification;

/**
 * Multishipping specification Test
 */
class Is3DSecureTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Object Manager helper
     *
     * @var \Magento\TestFramework\Helper\ObjectManager
     */
    protected $objectManager;

    /**
     * Payment config mock
     *
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Payment\Model\Config
     */
    protected $paymentConfigMock;

    /**
     * Store config mock
     *
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Core\Model\Store\Config
     */
    protected $storeConfigMock;

    public function setUp()
    {
        $this->paymentConfigMock = $this->getMock('\Magento\Payment\Model\Config', array(), array(), '', false);
        $this->storeConfigMock = $this->getMock('\Magento\Core\Model\Store\Config', array(), array(), '', false);
        $this->objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
    }

    /**
     * Test isSatisfiedBy method
     *
     * @param array $methodsInfo
     * @param bool $is3DSecureEnabled
     * @param bool $result
     * @dataProvider methodsDataProvider
     */
    public function testIsSatisfiedBy($methodsInfo, $is3DSecureEnabled, $result)
    {
        $method = 'method-name';
        $methodsInfo = array($method => $methodsInfo);

        $this->paymentConfigMock->expects($this->once())->method('getMethodsInfo')
            ->will($this->returnValue($methodsInfo));
        $this->storeConfigMock->expects($this->any())->method('getConfigFlag')
            ->will($this->returnValue($is3DSecureEnabled));

        $configSpecification = $this->objectManager->getObject(
            'Magento\Multishipping\Model\Payment\Method\Specification\Is3DSecure',
            array(
                'paymentConfig' => $this->paymentConfigMock,
                'storeConfig' => $this->storeConfigMock,
            )
        );

        $this->assertEquals(
            $result,
            $configSpecification->isSatisfiedBy($method),
            sprintf('Failed payment method test: "%s"', $method)
        );
    }

    /**
     * Data provider
     *
     * @return array
     */
    public function methodsDataProvider()
    {
        return array(
            array(
                array('allow_multiple_with_3dsecure' => 1), true, true,
            ),
            array(
                array('allow_multiple_with_3dsecure' => 1), false, true,
            ),
            array(
                array('allow_multiple_with_3dsecure' => 0), true, false,
            ),
            array(
                array('allow_multiple_with_3dsecure' => 0), false, true,
            ),
            array(
                array('no-flag' => 0), true, false,
            ),
        );
    }
}
