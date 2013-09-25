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
     * Core data
     *
     * @var Magento_Core_Helper_Data
     */
    protected $_coreData = null;

    /**
     * Entity attribute factory
     *
     * @var Magento_Eav_Model_Resource_Entity_AttributeFactory
     */
    protected $_entityAttributeFactory;

    /**
     * Eav resource helper
     *
     * @var Magento_Eav_Model_Resource_Helper
     */
    protected $_eavResourceHelper;

    /**
     * Construct
     *
     * @param Magento_Eav_Model_Resource_Entity_AttributeFactory $entityAttributeFactory
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Eav_Model_Resource_Helper $eavResourceHelper
     */
    public function __construct(
        Magento_Eav_Model_Resource_Entity_AttributeFactory $entityAttributeFactory,
        Magento_Core_Helper_Data $coreData,
        Magento_Eav_Model_Resource_Helper $eavResourceHelper
    ) {
        $this->_entityAttributeFactory = $entityAttributeFactory;
        $this->_coreData = $coreData;
        $this->_eavResourceHelper = $eavResourceHelper;
    }

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

        if ($this->_coreData->useDbCompatibleMode()) {
            $column['type']     = $attributeType;
            $column['is_null']  = true;
        } else {
            $column['type']     = $this->_eavResourceHelper->getDdlTypeByColumnType($attributeType);
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
        return $this->_entityAttributeFactory->create()
            ->getFlatUpdateSelect($this->getAttribute(), $store);
    }
}
