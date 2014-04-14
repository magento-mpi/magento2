<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Tax
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tax\Model\TaxClass\Source;

class Product extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    /**
     * Core data
     *
     * @var \Magento\Core\Helper\Data
     */
    protected $_coreData;

    /**
     * @var \Magento\Tax\Model\Resource\TaxClass\CollectionFactory
     */
    protected $_classesFactory;

    /**
     * @var \Magento\Eav\Model\Resource\Entity\Attribute\OptionFactory
     */
    protected $_optionFactory;

    /**
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Tax\Model\Resource\TaxClass\CollectionFactory $classesFactory
     * @param \Magento\Eav\Model\Resource\Entity\Attribute\OptionFactory $optionFactory
     */
    public function __construct(
        \Magento\Core\Helper\Data $coreData,
        \Magento\Tax\Model\Resource\TaxClass\CollectionFactory $classesFactory,
        \Magento\Eav\Model\Resource\Entity\Attribute\OptionFactory $optionFactory
    ) {
        $this->_coreData = $coreData;
        $this->_classesFactory = $classesFactory;
        $this->_optionFactory = $optionFactory;
    }

    /**
     * Get all options
     *
     * @return array
     */
    public function getAllOptions()
    {
        if (is_null($this->_options)) {
            /** @var $classCollection \Magento\Tax\Model\Resource\TaxClass\Collection */
            $classCollection = $this->_classesFactory->create();
            $classCollection->addFieldToFilter(
                'class_type',
                \Magento\Tax\Model\ClassModel::TAX_CLASS_TYPE_PRODUCT
            )->load();
            $this->_options = $classCollection->toOptionArray();
        }

        $options = $this->_options;
        array_unshift($options, array('value' => '0', 'label' => __('None')));
        return $options;
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

        $column['type'] = \Magento\DB\Ddl\Table::TYPE_INTEGER;
        $column['nullable'] = true;
        $column['comment'] = $attributeCode . ' tax column';

        return array($attributeCode => $column);
    }

    /**
     * Retrieve Select for update attribute value in flat table
     *
     * @param   int $store
     * @return  \Magento\DB\Select|null
     */
    public function getFlatUpdateSelect($store)
    {
        /** @var $option \Magento\Eav\Model\Resource\Entity\Attribute\Option */
        $option = $this->_optionFactory->create();
        return $option->getFlatUpdateSelect($this->getAttribute(), $store, false);
    }
}
