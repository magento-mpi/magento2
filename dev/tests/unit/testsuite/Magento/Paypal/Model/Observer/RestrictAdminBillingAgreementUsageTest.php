<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Paypal\Model\Observer;

class RestrictAdminBillingAgreementUsageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RestrictAdminBillingAgreementUsage
     */
    protected $_model;

    /**
     * @var \Magento\Framework\Event\Observer
     */
    protected $_observer;

    /**
     * @var \Magento\Framework\Object
     */
    protected $_event;

    /**
     * @var \Magento\Framework\AuthorizationInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_authorization;

    protected function setUp()
    {
        $this->_event = new \Magento\Framework\Object();

        $this->_observer = new \Magento\Framework\Event\Observer();
        $this->_observer->setEvent($this->_event);

        $this->_authorization = $this->getMockForAbstractClass('Magento\Framework\AuthorizationInterface');

        $objectManagerHelper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->_model = new \Magento\Paypal\Model\Observer\RestrictAdminBillingAgreementUsage($this->_authorization);
    }

    public function restrictAdminBillingAgreementUsageDataProvider()
    {
        return array(
            array(new \stdClass(), false, true),
            array(
                $this->getMockForAbstractClass(
                    'Magento\Paypal\Model\Payment\Method\Billing\AbstractAgreement',
                    array(),
                    '',
                    false
                ),
                true,
                true
            ),
            array(
                $this->getMockForAbstractClass(
                    'Magento\Paypal\Model\Payment\Method\Billing\AbstractAgreement',
                    array(),
                    '',
                    false
                ),
                false,
                false
            )
        );
    }

    /**
     * @param object $methodInstance
     * @param bool $isAllowed
     * @param bool $isAvailable
     * @dataProvider restrictAdminBillingAgreementUsageDataProvider
     */
    public function testExecute($methodInstance, $isAllowed, $isAvailable)
    {
        $this->_event->setMethodInstance($methodInstance);
        $this->_authorization->expects(
            $this->any()
        )->method(
                'isAllowed'
            )->with(
                'Magento_Paypal::use'
            )->will(
                $this->returnValue($isAllowed)
            );
        $result = new \stdClass();
        $result->isAvailable = true;
        $this->_event->setResult($result);
        $this->_model->execute($this->_observer);
        $this->assertEquals($isAvailable, $result->isAvailable);
    }
}
