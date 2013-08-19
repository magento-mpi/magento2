<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Source model for 'msrp_display_actual_price_type' product attribute
 *
 * @category   Magento
 * @package    Magento_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Catalog_Model_Product_Attribute_Source_Msrp_Type_Price
    extends Magento_Catalog_Model_Product_Attribute_Source_Msrp_Type
{
    /**
     * Get value from the store configuration settings
     */
    const TYPE_USE_CONFIG = '4';

    /**
     * Get all options
     *
     * @return array
     */
    public function getAllOptions()
    {
        if (!$this->_options) {
            $this->_options = parent::getAllOptions();
            $this->_options[] = array(
                'label' => __('Use config'),
                'value' => self::TYPE_USE_CONFIG
            );
        }
        return $this->_options;
    }

    /**
     * Retrieve flat column definition
     *
     * @return array
     */
    public function getFlatColums()
    {
        $attributeType = $this->getAttribute()->getBackendType();
        $attributeCode = $this->getAttribute()->getAttributeCode();
        $column = array(
            'unsigned'  => false,
            'default'   => null,
            'extra'     => null
        );

        if (Mage::helper('Magento_Core_Helper_Data')->useDbCompatibleMode()) {
            $column['type']     = $attributeType;
            $column['is_null']  = true;
        } else {
            $column['type']     = Mage::getResourceHelper('Magento_Eav')->getDdlTypeByColumnType($attributeType);
            $column['nullable'] = true;
        }

        return array($attributeCode => $column);
    }

    /**
     * Retrieve select for flat attribute update
     *
     * @param int $store
     * @return Magento_DB_Select|null
     */
    public function getFlatUpdateSelect($store)
    {
        return Mage::getResourceModel('Magento_Eav_Model_Resource_Entity_Attribute')
            ->getFlatUpdateSelect($this->getAttribute(), $store);
    }
}
