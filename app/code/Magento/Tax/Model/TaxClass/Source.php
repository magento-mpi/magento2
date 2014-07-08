<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tax\Model\TaxClass;

use Magento\Tax\Service\V1\Data\TaxClass;
use Magento\Tax\Service\V1\TaxClassServiceInterface;
use Magento\Framework\Service\V1\Data\SearchCriteriaBuilder;
use Magento\Framework\Service\V1\Data\FilterBuilder;

/**
 * Tax Class source model
 */
class Source implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * @var array
     */
    protected $_options;

    /**
     * @var TaxClassServiceInterface
     */
    protected $_taxClassService;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $_searchCriteriaBuilder;

    /**
     * @var FilterBuilder
     */
    protected $_filterBuilder;

    /**
     * @var \Magento\Framework\Convert\Object
     */
    protected $_converter;

    /**
     * @param TaxClassServiceInterface $taxClassService
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param FilterBuilder $filterBuilder
     * @param \Magento\Framework\Convert\Object $converter
     */
    public function __construct(
        TaxClassServiceInterface $taxClassService,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        FilterBuilder $filterBuilder,
        \Magento\Framework\Convert\Object $converter
    ) {
        $this->_taxClassService = $taxClassService;
        $this->_searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->_filterBuilder = $filterBuilder;
        $this->_converter = $converter;
    }

    /**
     * Return all existing Tax Classes as an option array
     *
     * @return array|void
     */
    public function toOptionArray()
    {
        if (!$this->_options) {
            $filters[] = $this->_filterBuilder
                ->setField('class_type')
                ->setValue(TaxClass::TYPE_CUSTOMER)
                ->create();
            $this->_searchCriteriaBuilder->addFilter($filters);
            $searchCriteria = $this->_searchCriteriaBuilder->create();
            $this->_options = $this->_converter->toOptionArray(
                $this->_taxClassService
                    ->searchTaxClass($searchCriteria)
                    ->getItems(),
                'classid',
                'classname'
            );
        }
        return $this->_options;
    }
}
