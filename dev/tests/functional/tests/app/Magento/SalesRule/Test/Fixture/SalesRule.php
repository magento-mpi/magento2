<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\SalesRule\Test\Fixture;

use Exception;
use Magento\Catalog\Test\Fixture\Product;
use Magento\Customer\Test\Fixture\Customer;
use Magento\SalesRule\Test\Repository\SalesRule as Repository;
use Mtf\Factory\Factory;
use Mtf\Fixture\DataFixture;

/**
 * Class SalesRule
 *
 */
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
     * @var int
     */
    private $salesRuleId = null;

    /**
     * {@inheritDoc}
     */
    protected function _initData()
    {
        $this->salesRuleId = -1;
        $this->_repository = Factory::getRepositoryFactory()->getMagentoSalesRuleSalesRule(
            $this->_dataConfig,
            $this->_data
        );
        $this->switchData(Repository::SIMPLE);
    }

    /**
     * Setup preconditions and persist
     *
     * @throws Exception
     */
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
        // Edit Customer just created
        $customerGridPage->open();
        $customerGrid = $customerGridPage->getCustomerGridBlock();
        $customerGrid->searchAndOpen(['email' => $this->customerFixture->getEmail()]);
        $customerEditPage = Factory::getPageFactory()->getCustomerIndexEdit();
        $editCustomerForm = $customerEditPage->getCustomerForm();
        // Set group to Retailer
        $editCustomerForm->openTab('account_information');
        $editCustomerForm->fillCustomer($this->customerFixture);
        // Save Customer Edit
        $customerEditPage->getPageActionsBlock()->save();
        // Create a product
        $this->productFixture = Factory::getFixtureFactory()->getMagentoCatalogSimpleProduct();
        $this->productFixture->persist();
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

    /**
     * @return int
     */
    public function getSalesRuleId()
    {
        return $this->salesRuleId;
    }

    /**
     * @param int $srid
     */
    public function setSalesRuleId($srid)
    {
        $this->salesRuleId = $srid;
    }
}
