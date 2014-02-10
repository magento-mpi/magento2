<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */


namespace Magento\Paypal\Model\Checkout;


class SessionPluginTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Paypal\Model\Checkout\SessionPlugin
     */
    protected $_model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_checkoutSessionMock;

    public function setUp()
    {
        $this->_checkoutSessionMock = $this->getMockBuilder('Magento\Checkout\Model\Session')
            ->disableOriginalConstructor()
            ->setMethods(array('setLastBillingAgreementId'))
            ->getMock();

        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->_model = $objectManager->getObject(
            'Magento\Paypal\Model\Checkout\SessionPlugin',
            array('checkoutSession' => $this->_checkoutSessionMock)
        );
    }

    public function testBeforeClearHelperData()
    {
        $this->_checkoutSessionMock->expects($this->once())
            ->method('setLastBillingAgreementId')
            ->with(null);
        $this->_model->beforeClearHelperData();
    }
}
 