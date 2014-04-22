<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Model\Resource\Customer\Grid;

use Magento\Core\Model\EntityFactory;
use Magento\Customer\Helper\View;
use Magento\Customer\Model\Resource\AbstractServiceCollection;
use Magento\Customer\Service\V1\CustomerAccountServiceInterface;
use Magento\Customer\Service\V1\Data\CustomerDetails;
use Magento\Framework\Service\V1\Data\FilterBuilder;
use Magento\Framework\Service\V1\Data\SearchCriteriaBuilder;

/**
 * Customer Grid Collection backed by Services
 */
class ServiceCollection extends AbstractServiceCollection
{
    /**
     * @var CustomerAccountServiceInterface
     */
    protected $accountService;

    /** @var View */
    protected $viewHelper;

    /**
     * @param EntityFactory $entityFactory
     * @param FilterBuilder $filterBuilder
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param CustomerAccountServiceInterface $accountService
     * @param View $viewHelper
     */
    public function __construct(
        EntityFactory $entityFactory,
        FilterBuilder $filterBuilder,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        CustomerAccountServiceInterface $accountService,
        View $viewHelper
    ) {
        parent::__construct($entityFactory, $filterBuilder, $searchCriteriaBuilder);
        $this->accountService = $accountService;
        $this->viewHelper = $viewHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function loadData($printQuery = false, $logQuery = false)
    {
        if (!$this->isLoaded()) {
            $searchCriteria = $this->getSearchCriteria();
            $searchResults = $this->accountService->searchCustomers($searchCriteria);
            $this->_totalRecords = $searchResults->getTotalCount();
            /** @var CustomerDetails[] $customers */
            $customers = $searchResults->getItems();
            foreach ($customers as $customer) {
                $this->_addItem($this->createCustomerDetailItem($customer));
            }
            $this->_setIsLoaded();
        }
        return $this;
    }

    /**
     * Creates a collection item that represents a customer for the customer Grid.
     *
     * @param CustomerDetails $customerDetail Input data for creating the item.
     * @return \Magento\Object Collection item that represents a customer
     */
    protected function createCustomerDetailItem(CustomerDetails $customerDetail)
    {
        $customer = $customerDetail->getCustomer();
        $customerItem = new \Magento\Object();
        $customerItem->setId($customer->getId());
        $customerItem->setEntityId($customer->getId());
        $customerItem->setName($this->viewHelper->getCustomerName($customer));
        $customerItem->setEmail($customer->getEmail());
        $customerItem->setWebsiteId($customer->getWebsiteId());
        $customerItem->setCreatedAt($customer->getCreatedAt());
        $customerItem->setGroupId($customer->getGroupId());

        $billingAddress = null;
        foreach ($customerDetail->getAddresses() as $address) {
            if ($address->isDefaultBilling()) {
                $billingAddress = $address;
                break;
            }
        }
        if ($billingAddress !== null) {
            $customerItem->setBillingTelephone($billingAddress->getTelephone());
            $customerItem->setBillingPostcode($billingAddress->getPostcode());
            $customerItem->setBillingCountryId($billingAddress->getCountryId());
            $region = is_null($billingAddress->getRegion()) ? '' : $billingAddress->getRegion()->getRegion();
            $customerItem->setBillingRegion($region);
        }
        return $customerItem;
    }
}
