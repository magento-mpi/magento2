<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tax\Model\TaxClass\Source;

use Magento\Tax\Model\Resource\TaxClass\CollectionFactory;
use Magento\Tax\Service\V1\Data\TaxClass;

class Customer extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    /**
     * @var \Magento\Tax\Service\V1\TaxRuleServiceInterface
     */
    protected $taxRuleService;

    /**
     * @var \Magento\Framework\Service\V1\Data\SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var \Magento\Framework\Service\V1\Data\FilterBuilder
     */
    protected $filterBuilder;

    /**
     * @param \Magento\Tax\Service\V1\TaxRuleServiceInterface $taxRuleService
     * @param \Magento\Framework\Service\V1\Data\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \Magento\Framework\Service\V1\Data\FilterBuilder $filterBuilder
     */
    public function __construct(
        \Magento\Tax\Service\V1\TaxRuleServiceInterface $taxRuleService,
        \Magento\Framework\Service\V1\Data\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Framework\Service\V1\Data\FilterBuilder $filterBuilder
    ) {
        $this->taxRuleService = $taxRuleService;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filterBuilder = $filterBuilder;
    }

    /**
     * @return array
     */
    public function getAllOptions()
    {
        if (!$this->_options) {
            $filter = $this->filterBuilder->setField(TaxClass::KEY_TYPE)->setValue('TYPE_CUSTOMER')->create();
            $searchCriteria = $this->searchCriteriaBuilder->addFilter([$filter])->create();
            $this->_options = $this->taxRuleService->searchTaxRules($searchCriteria);
        }
        return $this->_options;
    }
}