<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Paypal\Model\Hostedpro;

class RequestTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Paypal\Model\Hostedpro\Request
     */
    protected $_model;

    protected function setUp()
    {
        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->_model = $helper->getObject(
            'Magento\Paypal\Model\Hostedpro\Request'
        );
    }

    /**
     * @dataProvider addressesDataProvider
     */
    public function testSetOrderAddresses($billing, $shipping, $billingState, $state)
    {
        $payment = $this->getMock('Magento\Sales\Model\Order\Payment', ['__wakeup'], [], '', false);
        $order = $this->getMock(
            'Magento\Sales\Model\Order',
            ['getPayment', '__wakeup', 'getBillingAddress', 'getShippingAddress'],
            [],
            '',
            false
        );
        $order->expects($this->any())
            ->method('getPayment')
            ->will($this->returnValue($payment));
        $order->expects($this->any())
            ->method('getBillingAddress')
            ->will($this->returnValue($billing));
        $order->expects($this->any())
            ->method('getShippingAddress')
            ->will($this->returnValue($shipping));
        $this->_model->setOrder($order);
        $this->assertEquals($billingState, $this->_model->getData('billing_state'));
        $this->assertEquals($state, $this->_model->getData('state'));
    }

    /**
     * @return array
     */
    public function addressesDataProvider()
    {
        $billing = new \Magento\Framework\Object([
            'firstname' => 'Firstname',
            'lastname' => 'Lastname',
            'city' => 'City',
            'region_code' => 'CA',
            'postcode' => '12346',
            'country' => 'United States',
            'Street' => '1 Ln Ave'
        ]);
        $shipping = new \Magento\Framework\Object([
            'firstname' => 'ShipFirstname',
            'lastname' => 'ShipLastname',
            'city' => 'ShipCity',
            'region' => 'olala',
            'postcode' => '12346',
            'country' => 'United States',
            'Street' => '1 Ln Ave'
        ]);
        $billing2 = new \Magento\Framework\Object([
            'firstname' => 'Firstname',
            'lastname' => 'Lastname',
            'city' => 'City',
            'region_code' => 'muuuu',
            'postcode' => '12346',
            'country' => 'United States',
            'Street' => '1 Ln Ave'
        ]);
        $shipping2 = new \Magento\Framework\Object([
            'firstname' => 'ShipFirstname',
            'lastname' => 'ShipLastname',
            'city' => 'ShipCity',
            'postcode' => '12346',
            'country' => 'United States',
            'Street' => '1 Ln Ave'
        ]);
        return [
            [$billing, $shipping, 'CA', 'olala'],
            [$billing2, $shipping2, 'muuuu', 'ShipCity']
        ];
    }
}
