<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tax\Model\Rate;

use Magento\Tax\Service\V1\TaxRateServiceInterface;
use Magento\Framework\Data\SearchCriteriaBuilder;
use Magento\Framework\Convert\Object as Converter;
use Magento\Tax\Service\V1\Data\TaxRate;

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

    /** @var Converter */
    protected $converter;

    /**
     * Initialize dependencies.
     *
     * @param TaxRateServiceInterface $taxRateService
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param Converter $converter
     */
    public function __construct(
        TaxRateServiceInterface $taxRateService,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        Converter $converter
    ) {
        $this->taxRateService = $taxRateService;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->converter = $converter;
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
            $this->options = $this->converter->toOptionArray(
                $searchResults->getItems(),
                TaxRate::KEY_ID,
                TaxRate::KEY_CODE
            );
        }
        return $this->options;
    }
}
