<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Pbridge
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Pbridge\Model\Payment\Method;

class PbridgeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param bool|null $firstCaptureFlag
     * @dataProvider authorizeDataProvider
     */
    public function testAuthorize($firstCaptureFlag)
    {
        $infoOrder = $this->getMock('Magento\Sales\Model\Order', array('getQuoteId', '__wakeup'), array(), '', false);
        $infoInstance = $this->getMock('Magento\Payment\Model\Info', array('__wakeup'), array(), '', false);
        $infoInstance->setOrder($infoOrder);
        $originalMethodInstance = $this->getMockForAbstractClass(
            'Magento\Payment\Model\Method\AbstractMethod',
            array(),
            '',
            false,
            true,
            true,
            array('getConfigPaymentAction')
        );
        $requestHttp = $this->getMock('Magento\App\Request\Http', null, array(), '', false);
        $originalMethodInstance->setData('info_instance', $infoInstance);
        $order = $this->getMock('Magento\Sales\Model\Order', array(
            'getBillingAddress',
            'getStore',
            '__wakeup',
            'getShippingAddress'
        ), array(), '', false);
        $address = $this->getMock(
            'Magento\Customer\Model\Address\AbstractAddress',
            array('__wakeup'),
            array(),
            '',
            false
        );
        $order->expects($this->once())->method('getStore')->will($this->returnValue(new \Magento\Object()));
        $order->expects($this->once())->method('getBillingAddress')->will($this->returnValue($address));
        $order->expects($this->once())->method('getShippingAddress')->will($this->returnValue($address));
        $payment = $this->getMock('Magento\Sales\Model\Order\Payment', array('__wakeup'), array(), '', false);
        $payment->setFirstCaptureFlag($firstCaptureFlag)->setOrder($order);
        $region = $this->getMock('Magento\Directory\Model\Region', array('load', '__wakeup'), array(), '', false);
        $regionFactory = $this->getMock('Magento\Directory\Model\RegionFactory', array(
            'create',
            '__wakeup'
        ), array(), '', false);
        $regionFactory->expects($this->any())->method('create')->will($this->returnValue($region));
        $pbridgeData = $this->getMock('Magento\Pbridge\Helper\Data', array('prepareCart'), array(), '', false);
        $pbridgeData->expects($this->once())->method('prepareCart')->will($this->returnValue(array(array(), array())));
        $api = $this->getMock(
            'Magento\Pbridge\Model\Payment\Method\Pbridge\Api',
            array('doAuthorize', 'getResponse'),
            array(),
            '',
            false
        );
        // check fix for partial refunds in Payflow Pro
        $api->expects($this->once())
            ->method('doAuthorize')
            ->with(new ObjectConstraint('is_first_capture', isset($firstCaptureFlag) ? $firstCaptureFlag : true))
            ->will($this->returnSelf());

        $apiFactory = $this->getMock('Magento\Pbridge\Model\Payment\Method\Pbridge\ApiFactory', array('create'));
        $apiFactory->expects($this->once())->method('create')->will($this->returnValue($api));
        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);
        /** @var Pbridge $model */
        $model = $helper->getObject('Magento\Pbridge\Model\Payment\Method\Pbridge', array(
            'requestHttp' => $requestHttp,
            'regionFactory' => $regionFactory,
            'pbridgeData' => $pbridgeData,
            'pbridgeApiFactory' => $apiFactory
        ));
        $model->setOriginalMethodInstance($originalMethodInstance);
        $model->authorize($payment, 'any');
    }

    public function authorizeDataProvider()
    {
        return array(
            array(true),
            array(false),
            array(null),
        );
    }
}

class ObjectConstraint extends \PHPUnit_Framework_Constraint
{
    /**
     * @var mixed
     */
    protected $_key;

    /**
     * @var mixed
     */
    protected $_value;

    /**
     * @param mixed $key
     * @param mixed $value
     */
    public function __construct($key, $value)
    {
        $this->_key = $key;
        $this->_value = $value;
    }

    /**
     * Evaluates the constraint for parameter $other
     *
     * @param mixed $other
     * @param string $description
     * @param bool $returnResult
     * @return mixed
     * @throws \PHPUnit_Framework_ExpectationFailedException
     */
    public function evaluate($other, $description = '', $returnResult = false)
    {
        $equalConstraint = new \PHPUnit_Framework_Constraint_IsEqual($this->_value);
        return $equalConstraint->evaluate($other->getData($this->_key), $description, $returnResult);
    }

    /**
     * Returns a string representation of the constraint.
     *
     * @return string
     */
    public function toString()
    {
        return 'has ' . \PHPUnit_Util_Type::export($this->_value)
            . ' data at ' . \PHPUnit_Util_Type::export($this->_key) . ' key';
    }

    /**
     * Returns the description of the failure
     *
     * The beginning of failure messages is "Failed asserting that" in most
     * cases. This method should return the second part of that sentence.
     *
     * @param mixed $other
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function failureDescription($other)
    {
        return 'Magento\Object ' . $this->toString();
    }
}
