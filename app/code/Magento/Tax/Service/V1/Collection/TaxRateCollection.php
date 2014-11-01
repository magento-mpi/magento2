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
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Tax\Model\Calculation\Rate\Converter;
use Magento\Tax\Service\V1\TaxRateServiceInterface;
use Magento\Tax\Service\V1\Data\TaxRate;
use Magento\Framework\Api\SortOrderBuilder;

/**
 * Tax rate collection for a grid backed by Services
 */
class TaxRateCollection extends AbstractServiceCollection
{
    /**
     * @var TaxRateServiceInterface
     */
    protected $rateService;

    /**
     * @var Converter
     */
    protected $rateConverter;

    /**
     * Initialize dependencies.
     *
     * @param EntityFactory $entityFactory
     * @param FilterBuilder $filterBuilder
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param TaxRateServiceInterface $rateService
     * @param Converter $rateConverter
     * @param SortOrderBuilder $sortOrderBuilder
     */
    public function __construct(
        EntityFactory $entityFactory,
        FilterBuilder $filterBuilder,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        SortOrderBuilder $sortOrderBuilder,
        TaxRateServiceInterface $rateService,
        Converter $rateConverter
    ) {
        parent::__construct($entityFactory, $filterBuilder, $searchCriteriaBuilder, $sortOrderBuilder);
        $this->rateService = $rateService;
        $this->rateConverter = $rateConverter;
    }

    /**
     * {@inheritdoc}
     */
    public function loadData($printQuery = false, $logQuery = false)
    {
        if (!$this->isLoaded()) {
            $searchCriteria = $this->getSearchCriteria();
            $searchResults = $this->rateService->searchTaxRates($searchCriteria);
            $this->_totalRecords = $searchResults->getTotalCount();
            foreach ($searchResults->getItems() as $taxRate) {
                $this->_addItem($this->createTaxRateCollectionItem($taxRate));
            }
            $this->_setIsLoaded();
        }
        return $this;
    }

    /**
     * Creates a collection item that represents a tax rate for the tax rates grid.
     *
     * @param TaxRate $taxRate Input data for creating the item.
     * @return \Magento\Framework\Object Collection item that represents a tax rate
     */
    protected function createTaxRateCollectionItem(TaxRate $taxRate)
    {
        $collectionItem = new \Magento\Framework\Object();
        $collectionItem->setTaxCalculationRateId($taxRate->getId());
        $collectionItem->setCode($taxRate->getCode());
        $collectionItem->setTaxCountryId($taxRate->getCountryId());
        $collectionItem->setTaxRegionId($taxRate->getRegionId());
        $collectionItem->setRegionName($taxRate->getRegionName());
        $collectionItem->setTaxPostcode($taxRate->getPostcode());
        $collectionItem->setRate($taxRate->getPercentageRate());
        $collectionItem->setTitles($this->rateConverter->createTitleArrayFromServiceObject($taxRate));

        if ($taxRate->getZipRange() != null) {
            $zipRange = $taxRate->getZipRange();

            /* must be a "1" for existing code (e.g. JavaScript) to work */
            $collectionItem->setZipIsRange("1");
            $collectionItem->setZipFrom($zipRange->getFrom());
            $collectionItem->setZipTo($zipRange->getTo());
        } else {
            $collectionItem->setZipIsRange(null);
            $collectionItem->setZipFrom(null);
            $collectionItem->setZipTo(null);
        }

        return $collectionItem;
    }
}
