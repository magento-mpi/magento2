<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Multishipping\Block\Checkout;

use Magento\TestFramework\Helper\Bootstrap;

/**
 * @magentoAppArea frontend
 */
class AddressesTest extends \PHPUnit_Framework_TestCase
{
    const FIXTURE_CUSTOMER_ID = 1;

    /**
     * @var \Magento\Multishipping\Block\Checkout\Addresses
     */
    protected $_addresses;

    /**
     * @var \Magento\Framework\ObjectManager
     */
    protected $_objectManager;

    protected function setUp()
    {
        $this->_objectManager = Bootstrap::getObjectManager();
        $customerService = $this->_objectManager->create('Magento\Customer\Service\V1\CustomerAccountService');
        $customerData = $customerService->getCustomer(self::FIXTURE_CUSTOMER_ID);

        /** @var \Magento\Customer\Model\Session $customerSession */
        $customerSession = $this->_objectManager->get('Magento\Customer\Model\Session');
        $customerSession->setCustomerData($customerData);

        /** @var \Magento\Sales\Model\Resource\Quote\Collection $quoteCollection */
        $quoteCollection = $this->_objectManager->get('Magento\Sales\Model\Resource\Quote\Collection');
        /** @var $quote \Magento\Sales\Model\Quote */
        $quote = $quoteCollection->getLastItem();

        /** @var $checkoutSession \Magento\Checkout\Model\Session */
        $checkoutSession = $this->_objectManager->get('Magento\Checkout\Model\Session');
        $checkoutSession->setQuoteId($quote->getId());
        $checkoutSession->setLoadInactive(true);

        $this->_addresses = $this->_objectManager->create(
            'Magento\Multishipping\Block\Checkout\Addresses'
        );

    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoDataFixture Magento/Customer/_files/customer_address.php
     * @magentoDataFixture Magento/Checkout/_files/quote_with_product_and_payment.php
     */
    public function testGetAddressOptions()
    {
        $expectedResult = [
            [
                'value' => '1',
                'label' => 'John Smith, Green str, 67, CityM, Alabama 75477, United States'
            ]
        ];

        $addressAsHtml = $this->_addresses->getAddressOptions();
        $this->assertEquals($expectedResult, $addressAsHtml);
    }
}
