<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Bundle
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Bundle Price View Attribute Renderer
 *
 * @category    Magento
 * @package     Magento_Bundle
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Bundle_Model_Product_Attribute_Source_Price_View extends Magento_Eav_Model_Entity_Attribute_Source_Abstract
{
    /**
     * Core data
     *
     * @var Magento_Core_Helper_Data
     */
    protected $_coreData = null;

    /**
     * @param Magento_Core_Helper_Data $coreData
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData
    ) {
        $this->_coreData = $coreData;
    }

    /**
     * Get all options
     *
     * @return array
     */
    public function getAllOptions()
    {
        if (is_null($this->_options)) {
            $this->_options = array(
                array(
                    'label' => __('As Low as'),
                    'value' =>  1
                ),
                array(
                    'label' => __('Price Range'),
                    'value' =>  0
                ),
            );
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
        $options = $this->getAllOptions();
        foreach ($options as $option) {
            if ($option['value'] == $value) {
                return $option['label'];
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
            'unsigned'  => false,
            'default'   => null,
            'extra'     => null
        );

        if ($this->_coreData->useDbCompatibleMode()) {
            $column['type']     = 'int';
            $column['is_null']  = true;
        } else {
            $column['type']     = Magento_DB_Ddl_Table::TYPE_INTEGER;
            $column['nullable'] = true;
            $column['comment']  = 'Bundle Price View ' . $attributeCode . ' column';
        }

        return array($attributeCode => $column);
   }

    /**
     * Retrieve Select for update Attribute value in flat table
     *
     * @param   int $store
     * @return  Magento_DB_Select|null
     */
    public function getFlatUpdateSelect($store)
    {
        return Mage::getResourceModel('Magento_Eav_Model_Resource_Entity_Attribute_Option')
            ->getFlatUpdateSelect($this->getAttribute(), $store, false);
    }
}
