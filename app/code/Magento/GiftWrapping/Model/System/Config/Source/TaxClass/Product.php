<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftWrapping\Model\System\Config\Source\TaxClass;

use Magento\Tax\Service\V1\TaxClassServiceInterface;
use Magento\Tax\Service\V1\Data\TaxClass;
use Magento\Framework\Api\SearchCriteriaBuilder;
use \Magento\Framework\Api\FilterBuilder;

class Product implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var TaxClassServiceInterface
     */
    protected $taxClassService;

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
     * @param TaxClassServiceInterface $taxClassService
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param FilterBuilder $filterBuilder
     */
    public function __construct(
        TaxClassServiceInterface $taxClassService,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        FilterBuilder $filterBuilder
    ) {
        $this->taxClassService = $taxClassService;
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
            $this->options = array();

            $filter = $this->filterBuilder->setField(TaxClass::KEY_TYPE)
                ->setValue(TaxClassServiceInterface::TYPE_PRODUCT)
                ->setConditionType('=')
                ->create();
            $searchCriteria = $this->searchCriteriaBuilder->addFilter([$filter])->create();
            $taxClasses = $this->taxClassService->searchTaxClass($searchCriteria)->getItems();
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
