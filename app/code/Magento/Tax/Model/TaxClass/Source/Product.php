<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tax\Model\TaxClass\Source;

use Magento\Tax\Service\V1\Data\TaxClass;

class Product extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    /**
     * @var \Magento\Tax\Service\V1\taxClassServiceInterface
     */
    protected $_taxClassService;

    /**
     * @var \Magento\Framework\Service\V1\Data\SearchCriteriaBuilder
     */
    protected $_searchCriteriaBuilder;

    /**
     * @var \Magento\Framework\Service\V1\Data\FilterBuilder
     */
    protected $_filterBuilder;

    /**
     * Core data
     *
     * @var \Magento\Core\Helper\Data
     */
    protected $_coreData;

    /**
     * @var \Magento\Eav\Model\Resource\Entity\Attribute\OptionFactory
     */
    protected $_optionFactory;

    /**
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Eav\Model\Resource\Entity\Attribute\OptionFactory $optionFactory
     * @param \Magento\Tax\Service\V1\TaxClassServiceInterface $taxClassService
     * @param \Magento\Framework\Service\V1\Data\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \Magento\Framework\Service\V1\Data\FilterBuilder $filterBuilder
     */
    public function __construct(
        \Magento\Core\Helper\Data $coreData,
        \Magento\Tax\Model\Resource\TaxClass\CollectionFactory $classesFactory,
        \Magento\Eav\Model\Resource\Entity\Attribute\OptionFactory $optionFactory,
        \Magento\Tax\Service\V1\TaxClassServiceInterface $taxClassService,
        \Magento\Framework\Service\V1\Data\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Framework\Service\V1\Data\FilterBuilder $filterBuilder

    ) {
        $this->_coreData = $coreData;
        $this->_classesFactory = $classesFactory;
        $this->_optionFactory = $optionFactory;
        $this->_taxClassService = $taxClassService;
        $this->_filterBuilder = $filterBuilder;
        $this->_searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * @param bool $withDefaultValue
     * @return array
     */
    public function getAllOptions($withDefaultValue = true)
    {
        if (!$this->_options) {
            $filter = $this->_filterBuilder->setField(TaxClass::KEY_TYPE)->setValue(\Magento\Tax\Service\V1\Data\TaxClass::TYPE_PRODUCT)->create();
            $searchCriteria = $this->_searchCriteriaBuilder->addFilter([$filter])->create();
            $searchResults = $this->_taxClassService->searchTaxClass($searchCriteria);
            foreach ($searchResults->getItems() as $taxClass) {
                $this->_options[] = array(
                    'value' => $taxClass->getClassId(),
                    'label' => $taxClass->getClassName()
                );
            }
        }
        if ($withDefaultValue) {
            return array_merge(array(array('value' => '0', 'label' => __('None'))), $this->_options);
        }
        return $this->_options;
    }

    /**
     * Get a text for option value
     *
     * @param string|integer $value
     * @return string
     */
    public function getOptionText($value)
    {
        $options = $this->getAllOptions(false);

        foreach ($options as $item) {
            if ($item['value'] == $value) {
                return $item['label'];
            }
        }
        return false;
    }

    /**
     * Retrieve flat column definition
     *
     * @return array
     */
    public function getFlatColums()
    {
        $attributeCode = $this->getAttribute()->getAttributeCode();
        $column = array('unsigned' => true, 'default' => null, 'extra' => null);

        $column['type'] = \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER;
        $column['nullable'] = true;
        $column['comment'] = $attributeCode . ' tax column';

        return array($attributeCode => $column);
    }

    /**
     * Retrieve Select for update attribute value in flat table
     *
     * @param   int $store
     * @return  \Magento\Framework\DB\Select|null
     */
    public function getFlatUpdateSelect($store)
    {
        /** @var $option \Magento\Eav\Model\Resource\Entity\Attribute\Option */
        $option = $this->_optionFactory->create();
        return $option->getFlatUpdateSelect($this->getAttribute(), $store, false);
    }
}
