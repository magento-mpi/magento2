<?php
/**
 * Test class for \Magento\Sales\Block\Adminhtml\Order\Create\Form
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Block\Adminhtml\Order\Create;

use Magento\Customer\Service\V1;

/**
 * @magentoAppArea adminhtml
 */
class FormTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\Sales\Block\Adminhtml\Order\Create\Form */
    protected $_orderCreateBlock;

    /** @var \Magento\ObjectManager */
    protected $_objectManager;

    /**
     * @magentoDataFixture Magento/Sales/_files/quote.php
     */
    protected function setUp()
    {
        $this->_objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

        $sessionMock = $this->getMockBuilder(
            'Magento\Backend\Model\Session\Quote'
        )->disableOriginalConstructor()->setMethods(
            array('getCustomerId', 'getQuote', 'getStoreId', 'getStore')
        )->getMock();
        $sessionMock->expects($this->any())->method('getCustomerId')->will($this->returnValue(1));

        $quote = $this->_objectManager->create('Magento\Sales\Model\Quote')->load(1);
        $sessionMock->expects($this->any())->method('getQuote')->will($this->returnValue($quote));

        $sessionMock->expects($this->any())->method('getStoreId')->will($this->returnValue(1));

        $storeMock = $this->getMockBuilder(
            '\Magento\Store\Model\Store'
        )->disableOriginalConstructor()->setMethods(
            array('getCurrentCurrencyCode')
        )->getMock();
        $storeMock->expects($this->any())->method('getCurrentCurrencyCode')->will($this->returnValue('USD'));
        $sessionMock->expects($this->any())->method('getStore')->will($this->returnValue($storeMock));

        /** @var \Magento\View\LayoutInterface $layout */
        $layout = $this->_objectManager->get('Magento\View\LayoutInterface');
        $this->_orderCreateBlock = $layout->createBlock(
            'Magento\Sales\Block\Adminhtml\Order\Create\Form',
            'order_create_block' . rand(),
            array('sessionQuote' => $sessionMock)
        );
        parent::setUp();
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testOrderDataJson()
    {
        /** @var array $addressIds */
        $addressIds = $this->setUpMockAddress();
        $orderDataJson = $this->_orderCreateBlock->getOrderDataJson();
        $expectedOrderDataJson = <<<ORDER_DATA_JSON
        {
            "customer_id":1,
            "addresses":
                {"{$addressIds[0]}":
                    {"firstname":"John","lastname":"Smith","company":false,"street":"Green str, 67","city":"CityM",
                        "country_id":"US",
                        "region":"Alabama","region_id":1,
                        "postcode":"75477","telephone":"3468676","fax":false,"vat_id":false},
                 "{$addressIds[1]}":
                    {"firstname":"John","lastname":"Smith","company":false,"street":"Black str, 48","city":"CityX",
                        "country_id":"US",
                        "region":"Alabama","region_id":1,
                        "postcode":"47676","telephone":"3234676","fax":false,"vat_id":false}
                 },
             "store_id":1,"currency_symbol":"$","shipping_method_reseted":true,"payment_method":null
         }
ORDER_DATA_JSON;

        $this->assertEquals(json_decode($expectedOrderDataJson), json_decode($orderDataJson));
    }

    private function setUpMockAddress()
    {
        /** @var \Magento\Customer\Service\V1\Data\AddressBuilder $addressBuilder */
        $addressBuilder = $this->_objectManager->create('Magento\Customer\Service\V1\Data\AddressBuilder');
        /** @var \Magento\Customer\Service\V1\CustomerAddressServiceInterface $addressService */
        $addressService = $this->_objectManager->create('Magento\Customer\Service\V1\CustomerAddressServiceInterface');

        $addressData1 = $addressBuilder->setId(
            1
        )->setCountryId(
            'US'
        )->setCustomerId(
            1
        )->setDefaultBilling(
            true
        )->setDefaultShipping(
            true
        )->setPostcode(
            '75477'
        )->setRegion(
            new V1\Data\Region(
                (new V1\Data\RegionBuilder())->populateWithArray(
                    array('region_code' => 'AL', 'region' => 'Alabama', 'region_id' => 1)
                )
            )
        )->setStreet(
            array('Green str, 67')
        )->setTelephone(
            '3468676'
        )->setCity(
            'CityM'
        )->setFirstname(
            'John'
        )->setLastname(
            'Smith'
        )->create();

        $addressData2 = $addressBuilder->setId(
            2
        )->setCountryId(
            'US'
        )->setCustomerId(
            1
        )->setDefaultBilling(
            false
        )->setDefaultShipping(
            false
        )->setPostcode(
            '47676'
        )->setRegion(
            new V1\Data\Region(
                (new V1\Data\RegionBuilder())->populateWithArray(
                    array('region_code' => 'AL', 'region' => 'Alabama', 'region_id' => 1)
                )
            )
        )->setStreet(
            array('Black str, 48')
        )->setCity(
            'CityX'
        )->setTelephone(
            '3234676'
        )->setFirstname(
            'John'
        )->setLastname(
            'Smith'
        )->create();

        return $addressService->saveAddresses(1, array($addressData1, $addressData2));
    }
}
