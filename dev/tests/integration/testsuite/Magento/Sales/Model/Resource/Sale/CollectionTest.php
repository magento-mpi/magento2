<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Model\Resource\Sale;

class CollectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoDataFixture Magento/Sales/_files/order_with_customer.php
     */
    public function testSetCustomerFilter()
    {
        $_collectionModel = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Sales\Model\Resource\Sale\Collection');

        $this->assertEquals(1, $_collectionModel->setCustomerFilter(1)->count());

        $_collectionModel = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Sales\Model\Resource\Sale\Collection');

        $this->assertEquals(0, $_collectionModel->setCustomerFilter(2)->count());
    }
}
