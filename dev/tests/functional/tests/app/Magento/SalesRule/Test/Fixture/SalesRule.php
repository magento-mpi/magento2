<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\SalesRule\Test\Fixture;

use Exception;
use Magento\SalesRule\Test\Repository\SalesRule as Repository;
use Mtf\Fixture\DataFixture;
use Mtf\Factory\Factory;
use Magento\Customer\Test\Fixture\Customer;
use Magento\Catalog\Test\Fixture\Product;
use Magento\CustomerSegment\Test\Fixture\CustomerSegment;
use Magento\CustomerSegment\Test\Fixture\SegmentCondition;

class SalesRule extends DataFixture
{
    /**
     * @var Customer
     */
    protected $customerFixture;

    /**
     * @var Product
     */
    protected $productFixture;

    /**
     * @var CustomerSegment
     */
    protected $customerSegment;

    /**
     * @var int
     */
    protected $customerSegmentId;

    /**
     * @var SegmentCondition
     */
    protected $customerSegmentFixture;

    /**
     * Initialize fixture data
     */
    protected function _initData()
    {
        $this->_repository = Factory::getRepositoryFactory()->getMagentoSalesRuleSalesRule(
            $this->_dataConfig,
            $this->_data
        );
        $this->switchData(Repository::SIMPLE);
    }

    public function persist()
    {
        // Login to the backend
        Factory::getApp()->magentoBackendLoginUser();

        // Create a customer
        $this->customerFixture = Factory::getFixtureFactory()->getMagentoCustomerCustomer();
        $this->customerFixture->switchData('backend_retailer_customer');
        $this->customerFixture->persist();
        // Customer needs to be in a group and front end customer creation doesn't set group
        $customerGridPage = Factory::getPageFactory()->getCustomerIndex();
        $customerEditPage = Factory::getPageFactory()->getCustomerEdit();
        $customerGrid = $customerGridPage->getGridBlock();
        // Edit Customer just created
        $customerGridPage->open();
        $customerGrid->searchAndOpen(array('email' => $this->customerFixture->getEmail()));
        $editCustomerForm = $customerEditPage->getEditCustomerForm();
        // Set group to Retailer
        $editCustomerForm->openTab('customer_info_tabs_account');
        $editCustomerForm->fill($this->customerFixture);
        // Save Customer Edit
        $editCustomerForm->save();
        // Create a product
        $this->productFixture = Factory::getFixtureFactory()->getMagentoCatalogSimpleProduct();
        $this->productFixture->persist();
        // Create the customer segment
        $this->customerSegmentFixture = Factory::getFixtureFactory()->getMagentoCustomerSegmentCustomerSegment();
        $this->customerSegmentId = Factory::getApp()->magentoCustomerSegmentCustomerSegment(
            $this->customerSegmentFixture
        );
        if (empty($this->customerSegmentId)) {
            throw new Exception('No customer segment id returned by customer segment precondition');
        }
        // Create Customer Segment Condition
        $customerSegmentConditionFixture = Factory::getFixtureFactory()->getMagentoCustomerSegmentSegmentCondition();
        $customerSegmentConditionFixture->setPlaceHolders(
            array('segment_id' => $this->customerSegmentId, 'name' => $this->customerSegmentFixture->getSegmentName())
        );
        $customerSegmentConditionFixture->switchData('retailer_condition_curl');
        Factory::getApp()->magentoCustomerSegmentCustomerSegmentCondition($customerSegmentConditionFixture);
    }

    /**
     * Return the name of the sales rule represented by this fixture
     *
     * @return string
     */
    public function getSalesRuleName()
    {
        return $this->getData('fields/name/value');
    }

    /**
     * Return the discount applied
     *
     * @return string
     */
    public function getDiscount()
    {
        return $this->getData('fields/discount_amount/value');
    }

    /**
     * @return Customer
     */
    public function getCustomerFixture()
    {
        return $this->customerFixture;
    }

    /**
     * @return string
     */
    public function getCustomerSegmentId()
    {
        return $this->customerSegmentId;
    }

    /**
     * @return Product
     */
    public function getProductFixture()
    {
        return $this->productFixture;
    }

    /**
     * @return string
     */
    public function getProductPrice()
    {
        return $this->productFixture->getProductPrice();
    }
}
