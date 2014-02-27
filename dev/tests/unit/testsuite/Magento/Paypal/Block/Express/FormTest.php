<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Paypal\Block\Express;

class FormTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Paypal\Helper\Data|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_paypalData;

    /**
     * @var \Magento\Customer\Model\Session|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_customerSession;

    /**
     * @var \Magento\Paypal\Model\Config|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_paypalConfig;

    /**
     * @var Form
     */
    protected $_model;

    protected function setUp()
    {
        $this->_paypalData = $this->getMock('Magento\Paypal\Helper\Data', [], [], '', false);
        $this->_customerSession = $this->getMock('Magento\Customer\Model\Session', [], [], '', false);
        $this->_paypalConfig = $this->getMock('Magento\Paypal\Model\Config', [], [], '', false);
        $this->_paypalConfig->expects($this->once())
            ->method('setMethod')
            ->will($this->returnSelf());
        $paypalConfigFactory = $this->getMock('Magento\Paypal\Model\ConfigFactory', ['create'], [], '', false);
        $paypalConfigFactory->expects($this->once())->method('create')->will($this->returnValue($this->_paypalConfig));
        $mark = $this->getMock('Magento\View\Element\Template', [], [], '', false);
        $mark->expects($this->once())->method('setTemplate')->will($this->returnSelf());
        $mark->expects($this->any())->method('__call')->will($this->returnSelf());
        $layout = $this->getMockForAbstractClass('Magento\View\LayoutInterface');
        $layout->expects($this->once())
            ->method('createBlock')
            ->with('Magento\View\Element\Template')
            ->will($this->returnValue($mark));
        $localeResolver = $this->getMock('Magento\Locale\ResolverInterface', array(), array(), '', false, false);
        $appMock = $this->getMock('\Magento\Core\Model\App', array('getLocaleResolver'), array(), '', false);
        $appMock->expects($this->any())
            ->method('getLocaleResolver')
            ->will($this->returnValue($localeResolver));
        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->_model = $helper->getObject(
            'Magento\Paypal\Block\Express\Form',
            [
                'paypalData' => $this->_paypalData,
                'customerSession' => $this->_customerSession,
                'paypalConfigFactory' => $paypalConfigFactory,
                'layout' => $layout,
                'app' => $appMock,
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
        $this->_customerSession->expects($this->once())
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
