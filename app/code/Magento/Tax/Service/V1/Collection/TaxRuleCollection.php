<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tax\Service\V1\Collection;

use Magento\Core\Model\EntityFactory;
use Magento\Framework\Service\AbstractServiceCollection;
use Magento\Framework\Service\V1\Data\FilterBuilder;
use Magento\Framework\Service\V1\Data\SearchCriteriaBuilder;
use Magento\Tax\Model\Calculation\TaxRuleConverter;
use Magento\Tax\Service\V1\TaxRuleServiceInterface;
use Magento\Tax\Service\V1\Data\TaxRule;

/**
 * Tax rule collection for a grid backed by Services
 */
class TaxRuleCollection extends AbstractServiceCollection
{
    /**
     * @var TaxRuleServiceInterface
     */
    protected $ruleService;

    /**
     * @var TaxRuleConverter
     */
    protected $ruleConverter;

    /**
     * Initialize dependencies.
     *
     * @param EntityFactory $entityFactory
     * @param FilterBuilder $filterBuilder
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param TaxRuleServiceInterface $ruleService
     * @param TaxRuleConverter $ruleConverter
     */
    public function __construct(
        EntityFactory $entityFactory,
        FilterBuilder $filterBuilder,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        TaxRuleServiceInterface $ruleService,
        TaxRuleConverter $ruleConverter
    ) {
        parent::__construct($entityFactory, $filterBuilder, $searchCriteriaBuilder);
        $this->ruleService = $ruleService;
        $this->ruleConverter = $ruleConverter;
    }

    /**
     * {@inheritdoc}
     */
    public function loadData($printQuery = false, $logQuery = false)
    {
        if (!$this->isLoaded()) {
            $searchCriteria = $this->getSearchCriteria();
            $searchResults = $this->ruleService->searchTaxRules($searchCriteria);
            $this->_totalRecords = $searchResults->getTotalCount();
            foreach ($searchResults->getItems() as $taxRule) {
                $this->_addItem($this->createTaxRuleCollectionItem($taxRule));
            }
            $this->_setIsLoaded();
        }
        return $this;
    }

    /**
     * Creates a collection item that represents a tax rule for the tax rules grid.
     *
     * @param TaxRule $taxRule Input data for creating the item.
     * @return \Magento\Framework\Object Collection item that represents a tax rule
     */
    protected function createTaxRuleCollectionItem(TaxRule $taxRule)
    {
        $collectionItem = new \Magento\Framework\Object();
        $collectionItem->setTaxCalculationRuleId($taxRule->getId());
        $collectionItem->setCode($taxRule->getCode());
        $collectionItem->setPriority($taxRule->getPriority());
        $collectionItem->setPosition($taxRule->getSortOrder());
        $collectionItem->setCalculateSubtotal($taxRule->getCalculateSubtotal());
        $collectionItem->setCustomerTaxClasses($taxRule->getCustomerTaxClassIds());
        $collectionItem->setProductTaxClasses($taxRule->getProductTaxClassIds());
        $collectionItem->setTaxRates($taxRule->getTaxRateIds());
        return $collectionItem;
    }
}
