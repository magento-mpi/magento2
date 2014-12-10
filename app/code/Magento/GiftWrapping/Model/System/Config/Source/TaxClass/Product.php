<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\GiftWrapping\Model\System\Config\Source\TaxClass;

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Tax\Api\Data\TaxClassInterface as TaxClass;
use Magento\Tax\Api\TaxClassManagementInterface;
use Magento\Tax\Api\TaxClassRepositoryInterface;

class Product implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var TaxClassRepositoryInterface
     */
    protected $taxClassRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var \Magento\Framework\Api\FilterBuilder
     */
    protected $filterBuilder;

    /**
     * @var array
     */
    protected $options;

    /**
     * @param TaxClassRepositoryInterface $taxClassService
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param FilterBuilder $filterBuilder
     */
    public function __construct(
        TaxClassRepositoryInterface $taxClassService,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        FilterBuilder $filterBuilder
    ) {
        $this->taxClassRepository = $taxClassService;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filterBuilder = $filterBuilder;
    }

    /**
     * Retrieve list of products
     *
     * @return array
     */
    public function toOptionArray()
    {
        if (!$this->options) {
            $this->options = [];

            $filter = $this->filterBuilder->setField(TaxClass::KEY_TYPE)
                ->setValue(TaxClassManagementInterface::TYPE_PRODUCT)
                ->setConditionType('=')
                ->create();
            $searchCriteria = $this->searchCriteriaBuilder->addFilter([$filter])->create();
            $taxClasses = $this->taxClassRepository->getList($searchCriteria)->getItems();
            foreach ($taxClasses as $taxClass) {
                $this->options[] = [
                    'value' => $taxClass->getClassId(),
                    'label' => $taxClass->getClassName(),
                ];
            }
            array_unshift($this->options, ['value' => '0', 'label' => __('None')]);
        }
        return $this->options;
    }
}
