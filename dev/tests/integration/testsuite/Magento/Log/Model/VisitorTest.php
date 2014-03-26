<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Log\Model;

class VisitorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider createObserverWithCustomerDto
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testBindCustomerLogin(\Magento\Event\Observer $observer)
    {
        $customerDto = $observer->getEvent()->getCustomer();

        /** @var \Magento\Log\Model\Visitor $visitor */
        $visitor = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\Log\Model\Visitor');
        $visitor->bindCustomerLogin($observer);

        $this->assertTrue($visitor->getDoCustomerLogin());
        $this->assertEquals($customerDto->getId(), $visitor->getCustomerId());

        $visitor->unsetData();
        $visitor->setCustomerId('2');
        $visitor->bindCustomerLogin($observer);
        $this->assertNull($visitor->getDoCustomerLogin());
        $this->assertEquals('2', $visitor->getCustomerId());
    }

    /**
     * @dataProvider createObserverWithCustomerDto
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testBindCustomerLogout(\Magento\Event\Observer $observer)
    {
        /** @var \Magento\Log\Model\Visitor $visitor */
        $visitor = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\Log\Model\Visitor');
        $visitor->setCustomerId('1');
        $visitor->bindCustomerLogout($observer);
        $this->assertTrue($visitor->getDoCustomerLogout());

        $visitor->unsetData();
        $visitor->bindCustomerLogout($observer);
        $this->assertNull($visitor->getDoCustomerLogout());
    }

    /**
     * @return \Magento\Event\Observer
     */
    public function createObserverWithCustomerDto()
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $customer = $objectManager->create('Magento\Customer\Model\Customer')->load(1);
        $customerDto = $objectManager->create('Magento\Customer\Model\Converter')->createCustomerFromModel($customer);
        $event = new \Magento\Event(array('customer' => $customerDto));
        return array(
            array(new \Magento\Event\Observer(array('event' => $event)))
        );
    }

}
