<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Model\Order;

use Magento\TestFramework\Helper\Bootstrap;

class AddressTest extends \PHPUnit_Framework_TestCase
{
    /** @var Address */
    protected $_model;

    protected function setUp()
    {
        $this->_model = Bootstrap::getObjectManager()->create('Magento\Sales\Model\Order\Address');
    }

    /**
     * @magentoDataFixture Magento/Sales/_files/order.php
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoDataFixture Magento/Customer/_files/customer_address.php
     */
    public function testSave()
    {
        /** @var \Magento\Sales\Model\Order $order */
        $order = Bootstrap::getObjectManager()->create('Magento\Sales\Model\Order');
        /** @var \Magento\Customer\Service\V1\CustomerAddressServiceInterface $customerAddressService */
        $customerAddressService = Bootstrap::getObjectManager()->get(
            'Magento\Customer\Service\V1\CustomerAddressServiceInterface'
        );
        $order->loadByIncrementId('100000001');
        $this->_model->setOrder($order);
        $this->_model->setCustomerAddressData($customerAddressService->getAddress(1));
        $this->_model->save();
        $this->assertEquals($order->getId(), $this->_model->getParentId());
        $this->assertEquals($this->_model->getCustomerAddressId(), 1);
    }
}
