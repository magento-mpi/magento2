<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Model;

use Magento\Customer\Service\V1\Data\CustomerBuilder;
use Magento\Customer\Service\V1\Data\Customer;
use Magento\TestFramework\Helper\Bootstrap;

class QuoteTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @magentoDataFixture Magento/Catalog/_files/product_virtual.php
     * @magentoDataFixture Magento/Sales/_files/quote.php
     */
    public function testCollectTotalsWithVirtual()
    {
        $quote = Bootstrap::getObjectManager()
            ->create('Magento\Sales\Model\Quote');
        $quote->load('test01', 'reserved_order_id');

        $product = Bootstrap::getObjectManager()
            ->create('Magento\Catalog\Model\Product');
        $product->load(21);
        $quote->addProduct($product);
        $quote->collectTotals();

        $this->assertEquals(2, $quote->getItemsQty());
        $this->assertEquals(1, $quote->getVirtualItemsQty());
        $this->assertEquals(20, $quote->getGrandTotal());
        $this->assertEquals(20, $quote->getBaseGrandTotal());
    }

    public function testSetCustomerData()
    {
        /** @var \Magento\Sales\Model\Quote $quote */
        $quote = Bootstrap::getObjectManager()
            ->create('Magento\Sales\Model\Quote');
        $customerMetadataService = Bootstrap::getObjectManager()
            ->create('Magento\Customer\Service\V1\CustomerMetadataService');
        $customerBuilder = new CustomerBuilder($customerMetadataService);
        $expected = $this->_getCustomerDataArray();
        $customerBuilder->populateWithArray($expected);

        $customerDataSet = $customerBuilder->create();
        $this->assertEquals($expected, $customerDataSet->__toArray());
        $quote->setCustomerData($customerDataSet);

        $customerDataRetrieved = $quote->getCustomerData();
        $this->assertEquals($expected, $customerDataRetrieved->__toArray());
        $this->assertEquals('qa@example.com', $quote->getCustomerEmail());
    }

    public function testUpdateCustomerData()
    {
        /** @var \Magento\Sales\Model\Quote $quote */
        $quote = Bootstrap::getObjectManager()
            ->create('Magento\Sales\Model\Quote');
        $customerMetadataService = Bootstrap::getObjectManager()
            ->create('Magento\Customer\Service\V1\CustomerMetadataService');
        $customerBuilder = new CustomerBuilder($customerMetadataService);
        $expected = $this->_getCustomerDataArray();

        $customerBuilder->populateWithArray($expected);
        $customerDataSet = $customerBuilder->create();
        $this->assertEquals($expected, $customerDataSet->__toArray());
        $quote->setCustomerData($customerDataSet);

        $expected[Customer::EMAIL] = 'test@example.com';
        $customerBuilder->populateWithArray($expected);
        $customerDataUpdated = $customerBuilder->create();

        $quote->updateCustomerData($customerDataUpdated);
        $customerDataRetrieved = $quote->getCustomerData();
        $this->assertEquals($expected, $customerDataRetrieved->__toArray());
        $this->assertEquals('test@example.com', $quote->getCustomerEmail());
    }

    protected function _getCustomerDataArray()
    {
        return [
            Customer::ID => 1,
            Customer::CONFIRMATION => 'test',
            Customer::CREATED_AT => '2/3/2014',
            Customer::CREATED_IN => 'Default',
            Customer::DEFAULT_BILLING => 'test',
            Customer::DEFAULT_SHIPPING => 'test',
            Customer::DOB => '2/3/2014',
            Customer::EMAIL => 'qa@example.com',
            Customer::FIRSTNAME => 'Joe',
            Customer::GENDER => 'Male',
            Customer::GROUP_ID => \Magento\Customer\Service\V1\CustomerGroupService::NOT_LOGGED_IN_ID,
            Customer::LASTNAME => 'Dou',
            Customer::MIDDLENAME => 'Ivan',
            Customer::PREFIX => 'Dr.',
            Customer::STORE_ID => 1,
            Customer::SUFFIX => 'Jr.',
            Customer::TAXVAT => 1,
            Customer::WEBSITE_ID => 1
        ];
    }
}
