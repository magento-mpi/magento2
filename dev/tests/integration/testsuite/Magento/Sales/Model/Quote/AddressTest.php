<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Model\Quote;

use Magento\TestFramework\Helper\Bootstrap;

/**
 * @magentoDataFixture Magento/Customer/_files/customer.php
 * @magentoDataFixture Magento/Customer/_files/customer_two_addresses.php
 * @magentoDataFixture Magento/Sales/_files/quote.php
 */
class AddressTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\Sales\Model\Quote $quote */
    protected $_quote;

    /** @var \Magento\Customer\Model\Customer $customer */
    protected $_customer;

    /** @var \Magento\Sales\Model\Quote\Address */
    protected $_address;

    /**
     * Initialize quote and customer fixtures
     */
    public function setUp()
    {
        $this->_quote = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Sales\Model\Quote');
        $this->_quote->load('test01', 'reserved_order_id');
        $this->_quote->setIsMultiShipping('0');

        /** @var \Magento\Customer\Model\Customer $customer */
        $this->_customer = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Customer\Model\Customer');
        $this->_customer->load(1);

        /** @var \Magento\Sales\Model\Order\Address $address */
        $this->_address = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Sales\Model\Quote\Address');
        $this->_address->load(1);
    }

    /**
     * same_as_billing must be equal 0 if billing address is being saved
     *
     * @param bool $unsetId
     * @dataProvider unsetAddressIdDataProvider
     */
    public function testSameAsBillingForBillingAddress($unsetId)
    {
        $this->_quote->setCustomer($this->_customer);
        $address = $this->_quote->getBillingAddress();
        if ($unsetId) {
            $address->setId(null);
        }
        $address->setSameAsBilling(0)
            ->setCustomerAddress($this->_customer->getDefaultBillingAddress())
            ->save();
        $this->assertEquals(0, $this->_quote->getBillingAddress()->getSameAsBilling());
    }

    /**
     * same_as_billing must be equal 1 if customer is guest
     *
     * @param bool $unsetId
     * @dataProvider unsetAddressIdDataProvider
     */
    public function testSameAsBillingWhenCustomerIsGuest($unsetId)
    {
        $shippingAddress = $this->_quote->getShippingAddress();
        if ($unsetId) {
            $shippingAddress->setId(null);
        }
        $shippingAddress->setSameAsBilling(0);
        $shippingAddress->save();
        $this->assertEquals((int)$unsetId, $shippingAddress->getSameAsBilling());
    }

    /**
     * same_as_billing must be equal 1 if quote address has no customer address
     *
     * @param bool $unsetId
     * @dataProvider unsetAddressIdDataProvider
     */
    public function testSameAsBillingWhenQuoteAddressHasNoCustomerAddress($unsetId)
    {
        $this->_quote->setCustomer($this->_customer);
        $shippingAddress = $this->_quote->getShippingAddress();
        if ($unsetId) {
            $shippingAddress->setId(null);
        }
        $shippingAddress->setSameAsBilling(0)
            ->setCustomerAddress(null)
            ->save();
        $this->assertEquals((int)$unsetId, $this->_quote->getShippingAddress()->getSameAsBilling());
    }

    /**
     * same_as_billing must be equal 1 if customer registered and he has no default shipping address
     *
     * @param bool $unsetId
     * @dataProvider unsetAddressIdDataProvider
     */
    public function testSameAsBillingWhenCustomerHasNoDefaultShippingAddress($unsetId)
    {
        $this->_customer->setDefaultShipping(-1);
        $this->_quote->setCustomer($this->_customer);
        $this->_setCustomerAddressAndSave($unsetId);
        $this->assertEquals((int)$unsetId, $this->_quote->getShippingAddress()->getSameAsBilling());
    }

    /**
     * same_as_billing must be equal 1 if customer has the same billing and shipping address
     *
     * @param bool $unsetId
     * @dataProvider unsetAddressIdDataProvider
     */
    public function testSameAsBillingWhenCustomerHasBillingSameShipping($unsetId)
    {
        $this->_quote->setCustomer($this->_customer);
        $this->_setCustomerAddressAndSave($unsetId);
        $this->assertEquals((int)$unsetId, $this->_quote->getShippingAddress()->getSameAsBilling());
    }

    /**
     * same_as_billing must be equal 0 if customer has default shipping address that differs from default billing
     *
     * @param bool $unsetId
     * @dataProvider unsetAddressIdDataProvider
     */
    public function testSameAsBillingWhenCustomerHasDefaultShippingAddress($unsetId)
    {
        $this->_customer->setDefaultShipping(2);
        $this->_quote->setCustomer($this->_customer);
        $this->_setCustomerAddressAndSave($unsetId);
        $this->assertEquals(0, $this->_quote->getShippingAddress()->getSameAsBilling());
    }

    /**
     * Assign customer address to quote address and save quote address
     *
     * @param bool $unsetId
     */
    protected function _setCustomerAddressAndSave($unsetId)
    {
        $shippingAddress = $this->_quote->getShippingAddress();
        if ($unsetId) {
            $shippingAddress->setId(null);
        }
        $shippingAddress->setSameAsBilling(0)
            ->setCustomerAddress($this->_customer->getDefaultBillingAddress())
            ->save();
    }

    public function unsetAddressIdDataProvider()
    {
        return array(
            array(true),
            array(false),
        );
    }

    /**
     * Import customer address to quote address
     */
    public function testImportCustomerAddressData()
    {
        $street = ['Street1'];
        $city = 'TestCity';

        /** @var \Magento\Customer\Service\V1\Dto\AddressBuilder $addressBuilder */
        $addressBuilder = Bootstrap::getObjectManager()->create('Magento\Customer\Service\V1\Dto\AddressBuilder');
        $addressBuilderDto = $addressBuilder
            ->setCity($city)
            ->setStreet($street)
            ->create();

        $this->_address->importCustomerAddressData($addressBuilderDto);

        $this->assertEquals($street, $this->_address->getStreet(), 'Imported street is invalid.');
        $this->assertEquals($city, $this->_address->getCity(), 'Imported city is invalid.');
    }

    /**
     * Import customer address to quote address
     */
    public function testImportCustomerAddressDataWithCustomer()
    {
        $customerIdFromFixture = 1;
        $customerEmailFromFixture = 'customer@example.com';
        $city = 'TestCity';

        /** @var \Magento\Customer\Service\V1\Dto\AddressBuilder $addressBuilder */
        $addressBuilder = Bootstrap::getObjectManager()->create('Magento\Customer\Service\V1\Dto\AddressBuilder');
        $addressBuilderDto = $addressBuilder
            ->setCustomerId($customerIdFromFixture)
            ->setCity($city)
            ->create();

        $this->_address->importCustomerAddressData($addressBuilderDto);

        $this->assertEquals($customerEmailFromFixture, $this->_address->getEmail(), 'Email was imported incorrectly.');
        $this->assertEquals($city, $this->_address->getCity(), 'City was imported incorrectly.');
    }

    /**
     * Export customer address from quote address
     */
    public function testExportCustomerAddressData()
    {
        $street = ['Street1'];
        $company = 'TestCompany';

        $this->_address->setStreet($street);
        $this->_address->setCompany($company);

        $customerAddress = $this->_address->exportCustomerAddressData();

        $this->assertEquals($street, $customerAddress->getStreet(), 'Street was exported incorrectly.');
        $this->assertEquals($company, $customerAddress->getCompany(), 'Company was exported incorrectly.');
    }

    /**
     * Import order address to quote address
     */
    public function testImportOrderAddress()
    {
        $street = 'Street1';
        $email = 'test_email@example.com';

        /** @var \Magento\Sales\Model\Order\Address $orderAddress */
        $orderAddress = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Sales\Model\Order\Address');

        $orderAddress->setStreet($street);
        $orderAddress->setEmail($email);
        $this->_address->importOrderAddress($orderAddress);

        $this->assertEquals($street, $this->_address->getStreet1(), 'Expected street does not exists');
        $this->assertEquals($email, $orderAddress->getEmail(), 'Expected email does not exists');
    }
}
