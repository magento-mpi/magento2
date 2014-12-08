<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tax\Model;

use Magento\Customer\Service\V1\CustomerAccountService;
use Magento\Customer\Service\V1\CustomerAddressService;
use Magento\Customer\Service\V1\CustomerGroupService;

/**
 * Class CalculationTest
 *
 * @magentoDataFixture Magento/Customer/_files/customer.php
 * @magentoDataFixture Magento/Customer/_files/customer_address.php
 */
class CalculationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\TestFramework\ObjectManager
     */
    protected $_objectManager;

    /**
     * @var CustomerAccountService
     */
    protected $_customerAccountService;

    /**
     * @var CustomerAddressService
     */
    protected $_addressService;

    /**
     * @var CustomerGroupService
     */
    protected $_groupService;

    const FIXTURE_CUSTOMER_ID = 1;

    const FIXTURE_ADDRESS_ID = 1;

    /**
     * @var \Magento\Tax\Model\Calculation
     */
    protected $_model;

    protected function setUp()
    {
        /** @var $objectManager \Magento\TestFramework\ObjectManager */
        $this->_objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->_model = $this->_objectManager->create('Magento\Tax\Model\Calculation');
        $this->_customerAccountService = $this->_objectManager->create(
            'Magento\Customer\Service\V1\CustomerAccountService'
        );
        $this->_addressService = $this->_objectManager->create('Magento\Customer\Service\V1\CustomerAddressService');
        $this->_groupService = $this->_objectManager->create('Magento\Customer\Service\V1\CustomerGroupService');
    }

    public function testDefaultCustomerTaxClass()
    {
        $defaultCustomerTaxClass = 3;
        $this->assertEquals($defaultCustomerTaxClass, $this->_model->getDefaultCustomerTaxClass(null));
    }

    public function testGetDefaultRateRequest()
    {
        $customerDataSet = $this->_customerAccountService->getCustomer(self::FIXTURE_CUSTOMER_ID);
        $address = $this->_addressService->getAddress(self::FIXTURE_ADDRESS_ID);

        $rateRequest = $this->_model->getRateRequest(null, null, null, null, $customerDataSet->getId());

        $this->assertNotNull($rateRequest);
        $this->assertEquals($address->getCountryId(), $rateRequest->getCountryId());
        $this->assertEquals($address->getRegion()->getRegionId(), $rateRequest->getRegionId());
        $this->assertEquals($address->getPostcode(), $rateRequest->getPostcode());

        $customerTaxClassId = $this->_groupService->getGroup($customerDataSet->getGroupId())->getTaxClassId();
        $this->assertEquals($customerTaxClassId, $rateRequest->getCustomerClassId());
    }
}
