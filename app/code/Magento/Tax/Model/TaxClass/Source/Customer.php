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
     * @var \Magento\Tax\Service\V1\TaxClassServiceInterface
     */
    protected $taxClassService;

    /**
     * @var \Magento\Framework\Service\V1\Data\SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var \Magento\Framework\Service\V1\Data\FilterBuilder
     */
    protected $filterBuilder;

    /**
     * @param \Magento\Tax\Service\V1\TaxClassServiceInterface $taxClassService
     * @param \Magento\Framework\Service\V1\Data\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \Magento\Framework\Service\V1\Data\FilterBuilder $filterBuilder
     */
    public function __construct(
        \Magento\Tax\Service\V1\TaxClassServiceInterface $taxClassService,
        \Magento\Framework\Service\V1\Data\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Framework\Service\V1\Data\FilterBuilder $filterBuilder
    ) {
        $this->taxClassService = $taxClassService;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filterBuilder = $filterBuilder;
    }

    /**
     * @return array
     */
    public function getAllOptions()
    {
        if (!$this->_options) {
            $filter = $this->filterBuilder->setField(TaxClass::KEY_TYPE)->setValue(\Magento\Tax\Service\V1\Data\TaxClass::TYPE_CUSTOMER)->create();
            $searchCriteria = $this->searchCriteriaBuilder->addFilter([$filter])->create();
            $searchResults = $this->taxClassService->searchTaxClass($searchCriteria);
            foreach ($searchResults->getItems() as $taxClass) {
                $this->_options[] = array(
                    'value' => $taxClass->getClassId(),
                    'label' => $taxClass->getClassName()
                );
            }
        }
        return $this->_options;
    }
}