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

/**
 * Customer tax class source model.
 */
class Customer extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    /**
     * @var \Magento\Tax\Service\V1\TaxClassServiceInterface
     */
    protected $taxClassService;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var \Magento\Framework\Api\FilterBuilder
     */
    protected $filterBuilder;

    /**
     * Initialize dependencies.
     *
     * @param \Magento\Tax\Service\V1\TaxClassServiceInterface $taxClassService
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \Magento\Framework\Api\FilterBuilder $filterBuilder
     */
    public function __construct(
        \Magento\Tax\Service\V1\TaxClassServiceInterface $taxClassService,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Framework\Api\FilterBuilder $filterBuilder
    ) {
        $this->taxClassService = $taxClassService;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filterBuilder = $filterBuilder;
    }

    /**
     * Retrieve all customer tax classes as an options array.
     *
     * @param bool $withEmpty
     * @return array
     */
    public function getAllOptions($withEmpty = true)
    {
        if (!$this->_options) {
            $filter = $this->filterBuilder
                ->setField(TaxClass::KEY_TYPE)
                ->setValue(\Magento\Tax\Service\V1\TaxClassServiceInterface::TYPE_CUSTOMER)
                ->create();
            $searchCriteria = $this->searchCriteriaBuilder->addFilter([$filter])->create();
            $searchResults = $this->taxClassService->searchTaxClass($searchCriteria);
            foreach ($searchResults->getItems() as $taxClass) {
                $this->_options[] = array(
                    'value' => $taxClass->getClassId(),
                    'label' => $taxClass->getClassName()
                );
            }
        }

        if ($withEmpty) {
            if (!$this->_options) {
                return array(array('value' => '0', 'label' => __('None')));
            } else {
                return array_merge(array(array('value' => '0', 'label' => __('None'))), $this->_options);
            }
        }
        return $this->_options;
    }
}
