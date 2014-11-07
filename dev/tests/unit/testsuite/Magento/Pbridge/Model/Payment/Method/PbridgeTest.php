<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Pbridge\Model\Payment\Method;

class PbridgeTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\Pbridge\Helper\Data|PHPUnit_Framework_MockObject_MockObject */
    protected $pbridgeData;

    /** @var \Magento\Sales\Model\Order|PHPUnit_Framework_MockObject_MockObject */
    protected $order;

    /** @var \Magento\Pbridge\Model\Payment\Method\Pbridge\Api|PHPUnit_Framework_MockObject_MockObject */
    protected $api;

    /** @var \Magento\Pbridge\Model\Payment\Method\Pbridge|PHPUnit_Framework_MockObject_MockObject */
    protected $model;

    protected function setUp()
    {
        $infoOrder = $this->getMock('Magento\Sales\Model\Order', ['getQuoteId', '__wakeup'], [], '', false);
        $infoInstance = $this->getMock('Magento\Payment\Model\Info', ['__wakeup'], [], '', false);
        $infoInstance->setOrder($infoOrder);
        $originalMethodInstance = $this->getMockForAbstractClass(
            'Magento\Payment\Model\Method\AbstractMethod',
            [],
            '',
            false,
            true,
            true,
            ['getConfigPaymentAction']
        );
        $requestHttp = $this->getMock('Magento\Framework\App\Request\Http', null, [], '', false);
        $originalMethodInstance->setData('info_instance', $infoInstance);
        $this->order = $this->getMock(
            'Magento\Sales\Model\Order',
            ['getBillingAddress', 'getStore', '__wakeup', 'getShippingAddress', 'getCustomerId', 'getBaseTotalDue'],
            [],
            '',
            false
        );
        $address = $this->getMock(
            'Magento\Customer\Model\Address\AbstractAddress',
            ['__wakeup'],
            [],
            '',
            false
        );
        $this->order->expects(
            $this->any()
        )->method(
                'getStore'
            )->will(
                $this->returnValue(new \Magento\Framework\Object(['id' => 1]))
            );
        $this->order->expects($this->any())->method('getBillingAddress')->will($this->returnValue($address));
        $this->order->expects($this->any())->method('getShippingAddress')->will($this->returnValue($address));
        $this->order->expects($this->any())->method('getCustomerId')->will($this->returnValue(1));
        $region = $this->getMock('Magento\Directory\Model\Region', ['load', '__wakeup'], [], '', false);
        $regionFactory = $this->getMock(
            'Magento\Directory\Model\RegionFactory',
            ['create', '__wakeup'],
            [],
            '',
            false
        );
        $regionFactory->expects($this->any())->method('create')->will($this->returnValue($region));
        $this->pbridgeData = $this->getMock(
            'Magento\Pbridge\Helper\Data',
            ['prepareCart', 'getCustomerIdentifierByEmail'],
            [],
            '',
            false
        );

        $this->api = $this->getMock(
            'Magento\Pbridge\Model\Payment\Method\Pbridge\Api',
            ['doAuthorize', 'doVoid', 'getResponse'],
            [],
            '',
            false
        );

        $apiFactory = $this->getMock(
            'Magento\Pbridge\Model\Payment\Method\Pbridge\ApiFactory',
            ['create'],
            [],
            '',
            false
        );
        $apiFactory->expects($this->any())->method('create')->will($this->returnValue($this->api));
        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->model = $helper->getObject(
            'Magento\Pbridge\Model\Payment\Method\Pbridge',
            [
                'requestHttp' => $requestHttp,
                'regionFactory' => $regionFactory,
                'pbridgeData' => $this->pbridgeData,
                'pbridgeApiFactory' => $apiFactory
            ]
        );
        $this->model->setOriginalMethodInstance($originalMethodInstance);
    }

    /**
     * @param bool|null $firstCaptureFlag
     * @dataProvider authorizeDataProvider
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function testAuthorize($firstCaptureFlag)
    {
        $payment = $this->getMock('Magento\Sales\Model\Order\Payment', ['__wakeup'], [], '', false);
        $payment->setFirstCaptureFlag($firstCaptureFlag)->setOrder($this->order);
        $this->pbridgeData->expects($this->once())
            ->method('prepareCart')
            ->will($this->returnValue([[], []]));
        $this->pbridgeData->expects(
            $this->once()
        )->method(
                'getCustomerIdentifierByEmail'
            )->with(
                $this->equalTo(1),
                $this->equalTo(1)
            )->will(
                $this->returnValue(null)
            );
        // check fix for partial refunds in Payflow Pro
        $this->api->expects(
            $this->once()
        )->method(
                'doAuthorize'
            )->with(
                new ObjectConstraint('is_first_capture', isset($firstCaptureFlag) ? $firstCaptureFlag : true)
            )->will(
                $this->returnSelf()
            );

        $this->model->authorize($payment, 'any');
    }

    public function authorizeDataProvider()
    {
        return [[true], [false], [null]];
    }

    /**
     * @param int $authTransactionId
     * @param mixed $result
     * @dataProvider voidDataProvider
     */
    public function testVoid($authTransactionId, $result)
    {
        $payment = new \Magento\Framework\Object();
        $payment->setParentTransactionId($authTransactionId);
        if (!$authTransactionId) {
            $this->setExpectedException('\Exception', 'You need an authorization transaction to void.');
        } else {
            $payment->setOrder($this->order);
            $this->api->expects($this->once())->method('doVoid');
            $this->api->expects($this->once())->method('getResponse')->will($this->returnValue($result));
        }
        $this->assertEquals($result, $this->model->void($payment));
    }

    public function voidDataProvider()
    {
        return [[1, 'api response'], [0, null]];
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
        return 'has ' . \PHPUnit_Util_Type::export(
            $this->_value
        ) . ' data at ' . \PHPUnit_Util_Type::export(
            $this->_key
        ) . ' key';
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
        return 'Magento\Framework\Object ' . $this->toString();
    }
}
