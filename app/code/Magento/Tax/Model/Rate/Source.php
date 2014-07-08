<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tax\Model\Rate;

use Magento\Tax\Service\V1\TaxRateServiceInterface;
use Magento\Framework\Service\V1\Data\SearchCriteriaBuilder;

/**
 * Tax rate source model.
 */
class Source implements \Magento\Framework\Data\OptionSourceInterface
{
    /** @var array */
    protected $options;

    /** @var TaxRateServiceInterface */
    protected $taxRateService;

    /** @var SearchCriteriaBuilder */
    protected $searchCriteriaBuilder;

    /**
     * Initialize dependencies.
     *
     * @param TaxRateServiceInterface $taxRateService
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        TaxRateServiceInterface $taxRateService,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->taxRateService = $taxRateService;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * Retrieve all tax rates as an options array.
     *
     * @return array
     */
    public function toOptionArray()
    {
        if (!$this->options) {
            $searchCriteria = $this->searchCriteriaBuilder->create();
            $searchResults = $this->taxRateService->searchTaxRates($searchCriteria);
            foreach ($searchResults->getItems() as $taxRate) {
                $this->options[] = array(
                    'value' => $taxRate->getId(),
                    'label' => $taxRate->getCode()
                );
            }
        }
        return $this->options;
    }
}
