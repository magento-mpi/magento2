<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Paypal\Helper;


class DataTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_paymentDataMock;

    /**
     * @var \Magento\Paypal\Helper\Data
     */
    protected $_helper;

    public function setUp()
    {
        $this->_paymentDataMock = $this->getMockBuilder('Magento\Payment\Helper\Data')
            ->disableOriginalConstructor()
            ->setMethods(array('getStoreMethods', 'getPaymentMethods'))
            ->getMock();

        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->_helper = $objectManager->getObject(
            'Magento\Paypal\Helper\Data',
            array(
                'paymentData' => $this->_paymentDataMock
            )
        );
    }

    /**
     * @dataProvider getBillingAgreementMethodsDataProvider
     * @param $store
     * @param $quote
     * @param $paymentMethods
     * @param $expectedResult
     */
    public function testGetBillingAgreementMethods($store, $quote, $paymentMethods, $expectedResult)
    {
        $this->_paymentDataMock->expects($this->any())
            ->method('getStoreMethods')
            ->with($store, $quote)
            ->will($this->returnValue($paymentMethods));
        $this->assertEquals($expectedResult, $this->_helper->getBillingAgreementMethods($store, $quote));
    }

    /**
     * @dataProvider getAllBillingAgreementMethodsDataProvider
     * @param $paymentMethods
     * @param $expectedResult
     */
    public function testGetAllBillingAgreementMethods($paymentMethods, $expectedResult)
    {
        $this->_paymentDataMock->expects($this->any())
            ->method('getPaymentMethods')
            ->will($this->returnValue($paymentMethods));
        $this->assertEquals($expectedResult, $this->_helper->getAllBillingAgreementMethods());
    }

    /**
     * @dataProvider canManageBillingAgreementsDataProvider
     * @param $expectedResult
     * @param $methodInstance
     */
    public function testCanManageBillingAgreements($expectedResult, $methodInstance)
    {
        $this->assertEquals($expectedResult, $this->_helper->canManageBillingAgreements($methodInstance));
    }

    /**
     * @return array
     */
    public function getBillingAgreementMethodsDataProvider()
    {
        $quoteMock = $this->getMockBuilder('Magento\Sales\Model\Quote')
            ->disableOriginalConstructor()
            ->setMethods(null);
        $methodInterfaceMock = $this->getMockBuilder('Magento\Paypal\Model\Billing\Agreement\MethodInterface')
            ->getMock();

        return array(
            array('1', $quoteMock, array($methodInterfaceMock), array($methodInterfaceMock)),
            array('1', $quoteMock, array(new \StdClass()), array())
        );
    }

    /**
     * @return array
     */
    public function getAllBillingAgreementMethodsDataProvider()
    {
        $methodInterfaceMock = $this->getMockBuilder('Magento\Paypal\Model\Billing\Agreement\MethodInterface')
            ->getMock();
        return array(
            array(
                array(
                    'code1' => array('model' => $methodInterfaceMock, 'title' => 'title1'),
                    'code2' => array('title' => 'title2'),
                    'code3' => array('model' => new \StdClass(), 'title' => 'title3'),
                ),
                array('code1' => 'title1')
            ),
            array(
                array('code' => array('title' => 'title')),
                array()
            )
        );
    }

    /**
     * @return array
     */
    public function canManageBillingAgreementsDataProvider()
    {
        $methodInterfaceMock = $this->getMockBuilder('Magento\Paypal\Model\Billing\Agreement\MethodInterface')
            ->getMock();
        return array(
            array(true, $methodInterfaceMock),
            array(false, new \StdClass())
        );
    }
}
 