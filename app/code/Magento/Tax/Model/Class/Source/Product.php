<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Tax
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Tax_Model_Class_Source_Product extends Magento_Eav_Model_Entity_Attribute_Source_Abstract
{
    /**
     * Core data
     *
     * @var Magento_Core_Helper_Data
     */
    protected $_coreData;

    /**
     * @var Magento_Tax_Model_Resource_Class_CollectionFactory
     */
    protected $_classesFactory;

    /**
     * @var Magento_Eav_Model_Resource_Entity_Attribute_OptionFactory
     */
    protected $_optionFactory;

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Tax_Model_Resource_Class_CollectionFactory $classesFactory
     * @param Magento_Eav_Model_Resource_Entity_Attribute_OptionFactory $optionFactory
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Tax_Model_Resource_Class_CollectionFactory $classesFactory,
        Magento_Eav_Model_Resource_Entity_Attribute_OptionFactory $optionFactory
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
            /** @var $classCollection Magento_Tax_Model_Resource_Class_Collection */
            $classCollection = $this->_classesFactory->create();
            $classCollection->addFieldToFilter('class_type', Magento_Tax_Model_Class::TAX_CLASS_TYPE_PRODUCT)->load();
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
        $column = array(
            'unsigned'  => true,
            'default'   => null,
            'extra'     => null
        );

        if ($this->_coreData->useDbCompatibleMode()) {
            $column['type']     = 'int';
            $column['is_null']  = true;
        } else {
            $column['type']     = Magento_DB_Ddl_Table::TYPE_INTEGER;
            $column['nullable'] = true;
            $column['comment']  = $attributeCode . ' tax column';
        }

        return array($attributeCode => $column);
   }

    /**
     * Retrieve Select for update attribute value in flat table
     *
     * @param   int $store
     * @return  Magento_DB_Select|null
     */
    public function getFlatUpdateSelect($store)
    {
        /** @var $option Magento_Eav_Model_Resource_Entity_Attribute_Option */
        $option = $this->_optionFactory->create();
        return $option->getFlatUpdateSelect($this->getAttribute(), $store, false);
    }
}
