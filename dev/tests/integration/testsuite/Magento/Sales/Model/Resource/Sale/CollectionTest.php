<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Model\Resource\Sale;

use Magento\TestFramework\Helper\Bootstrap;

class CollectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @magentoDataFixture Magento/Sales/_files/order_with_customer.php
     */
    public function testSetCustomerFilter()
    {
        $collectionModel = Bootstrap::getObjectManager()->create('Magento\Sales\Model\Resource\Sale\Collection');
        $this->assertEquals(1, $collectionModel->setCustomerFilter(1)->count());
        $collectionModel = Bootstrap::getObjectManager()->create('Magento\Sales\Model\Resource\Sale\Collection');
        $this->assertEquals(0, $collectionModel->setCustomerFilter(2)->count());
    }
}
