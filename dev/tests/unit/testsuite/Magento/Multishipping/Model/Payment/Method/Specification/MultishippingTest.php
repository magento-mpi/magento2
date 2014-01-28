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
class MultishippingTest extends \PHPUnit_Framework_TestCase
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
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $paymentConfig;

    /**
     * Store config mock
     *
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeConfig;

    /**
     * Set up
     */
    public function setUp()
    {
        $this->paymentConfig = $this->getMock('\Magento\Payment\Model\Config', array(), array(), '', false);
        $this->storeConfig = $this->getMock('\Magento\Core\Model\Store\Config', array(), array(), '', false);
        $this->objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
    }

    /**
     * Test isSatisfiedBy method
     *
     * @param string $method
     * @param bool $is3DSecureEnabled
     * @param bool $result
     * @dataProvider methodsDataProvider
     */
    public function testIsSatisfiedBy($method, $is3DSecureEnabled, $result)
    {
        $this->paymentConfig->expects($this->once())->method('getMethodsInfo')
            ->will($this->returnValue($this->getPaymentMethodConfig()));

        $this->storeConfig->expects($this->any())->method('getConfigFlag')
            ->will($this->returnValue($is3DSecureEnabled));

        $configSpecification = $this->objectManager->getObject(
            'Magento\Multishipping\Model\Payment\Method\Specification\Multishipping',
            array(
                'paymentConfig' => $this->paymentConfig,
                'coreStoreConfig' => $this->storeConfig,
            )
        );

        $this->assertEquals($result,
            $configSpecification->isSatisfiedBy($method),
            sprintf('Failed payment method test: "%s"', $method)
        );
    }

    /**
     * Get payment methods config data
     *
     * @return array
     */
    protected function getPaymentMethodConfig()
    {
        return array(
            'allow_all' => array(
                'allow_multiple_address' => 1,
                'allow_multiple_with_3dsecure' => 1,
            ),
            'deny_3d' => array(
                'allow_multiple_address' => 1,
                'allow_multiple_with_3dsecure' => 0,
            ),
            'allow_3d_only' => array(
                'allow_multiple_address' => 0,
                'allow_multiple_with_3dsecure' => 1,
            ),
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
            array('allow_all', false, true),
            array('allow_all', true, true),
            array('deny_3d', false, true),
            array('deny_3d', true, false),
            array('allow_3d_only', true, false),
            array('allow_3d_only', false, false),
            array('no_method', false, false),
            array('no_method', true, false),
        );
    }
}
