<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Paypal\Block\Express;

class FormTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Paypal\Helper\Data|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_paypalData;

    /**
     * @var \Magento\Paypal\Model\Config|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_paypalConfig;

    /**
     * @var \Magento\Customer\Helper\Session\CurrentCustomer|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $currentCustomer;

    /**
     * @var Form
     */
    protected $_model;

    protected function setUp()
    {
        $this->_paypalData = $this->getMock('Magento\Paypal\Helper\Data', [], [], '', false);
        $this->_paypalConfig = $this->getMock('Magento\Paypal\Model\Config', [], [], '', false);
        $this->_paypalConfig->expects($this->once())
            ->method('setMethod')
            ->will($this->returnSelf());
        $paypalConfigFactory = $this->getMock('Magento\Paypal\Model\ConfigFactory', ['create'], [], '', false);
        $paypalConfigFactory->expects($this->once())->method('create')->will($this->returnValue($this->_paypalConfig));
        $mark = $this->getMock('Magento\Framework\View\Element\Template', [], [], '', false);
        $mark->expects($this->once())->method('setTemplate')->will($this->returnSelf());
        $mark->expects($this->any())->method('__call')->will($this->returnSelf());
        $layout = $this->getMockForAbstractClass('Magento\Framework\View\LayoutInterface');
        $layout->expects($this->once())
            ->method('createBlock')
            ->with('Magento\Framework\View\Element\Template')
            ->will($this->returnValue($mark));
        $this->currentCustomer = $this->getMockBuilder('\Magento\Customer\Helper\Session\CurrentCustomer')
            ->disableOriginalConstructor()
            ->getMock();

        $localeResolver = $this->getMock(
            'Magento\Framework\Locale\ResolverInterface',
            [],
            [],
            '',
            false,
            false
        );
        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->_model = $helper->getObject(
            'Magento\Paypal\Block\Express\Form',
            [
                'paypalData' => $this->_paypalData,
                'paypalConfigFactory' => $paypalConfigFactory,
                'currentCustomer' => $this->currentCustomer,
                'layout' => $layout,
                'localeResolver' => $localeResolver
            ]
        );
    }

    /**
     * @param bool $ask
     * @param string|null $expected
     * @dataProvider getBillingAgreementCodeDataProvider
     */
    public function testGetBillingAgreementCode($ask, $expected)
    {
        $this->currentCustomer->expects($this->once())
            ->method('getCustomerId')
            ->will($this->returnValue('customer id'));
        $this->_paypalData->expects($this->once())
            ->method('shouldAskToCreateBillingAgreement')
            ->with($this->identicalTo($this->_paypalConfig), 'customer id')
            ->will($this->returnValue($ask));
        $this->assertEquals($expected, $this->_model->getBillingAgreementCode());
    }

    public function getBillingAgreementCodeDataProvider()
    {
        return [
            [true, \Magento\Paypal\Model\Express\Checkout::PAYMENT_INFO_TRANSPORT_BILLING_AGREEMENT],
            [false, null]
        ];
    }
}
