<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftCard
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_GiftCard_Model_Source_Open extends Magento_Eav_Model_Entity_Attribute_Source_Abstract
{
    /**
     * Core data
     *
     * @var Magento_Core_Helper_Data
     */
    protected $_coreData = null;

    /**
     * Eav entity attribute factory
     *
     * @var Magento_Eav_Model_Resource_Entity_AttributeFactory
     */
    protected $_eavAttributeFactory;

    /**
     * Resource helper
     *
     * @var Magento_Eav_Model_Resource_Helper_Mysql4
     */
    protected $_resourceHelper;

    /**
     * @param Magento_Eav_Model_Resource_Entity_AttributeFactory $eavAttributeFactory
     * @param Magento_Eav_Model_Resource_Helper_Mysql4 $resourceHelper
     * @param Magento_Core_Helper_Data $coreData
     */
    public function __construct(
        Magento_Eav_Model_Resource_Helper_Mysql4 $resourceHelper,
        Magento_Eav_Model_Resource_Entity_AttributeFactory $eavAttributeFactory,
        Magento_Core_Helper_Data $coreData
    ) {
        $this->_resourceHelper = $resourceHelper;
        $this->_eavAttributeFactory = $eavAttributeFactory;
        $this->_coreData = $coreData;
    }

    /**
     * Get all options
     *
     * @return array
     */
    public function getAllOptions()
    {
        $result = array();
        foreach ($this->_getValues() as $k => $v) {
            $result[] = array(
                'value' => $k,
                'label' => $v,
            );
        }

        return $result;
    }

    /**
     * Get option text
     *
     * @param int|string $value
     * @return bool|null|string
     */
    public function getOptionText($value)
    {
        $options = $this->_getValues();
        if (isset($options[$value])) {
            return $options[$value];
        }
        return null;
    }

    /**
     * Get values
     *
     * @return array
     */
    protected function _getValues()
    {
        return array(
            Magento_GiftCard_Model_Giftcard::OPEN_AMOUNT_DISABLED => __('No'),
            Magento_GiftCard_Model_Giftcard::OPEN_AMOUNT_ENABLED  => __('Yes'),
        );
    }

    /**
     * Retrieve flat column definition
     *
     * @return array
     */
    public function getFlatColums()
    {
        $attributeDefaultValue = $this->getAttribute()->getDefaultValue();
        $attributeCode = $this->getAttribute()->getAttributeCode();
        $attributeType = $this->getAttribute()->getBackendType();
        $isNullable = is_null($attributeDefaultValue) || empty($attributeDefaultValue);

        $column = array(
            'unsigned' => false,
            'extra'    => null,
            'default'  => $isNullable ? null : $attributeDefaultValue
        );

        if ($this->_coreData->useDbCompatibleMode()) {
            $column['type']     = $attributeType;
            $column['is_null']  = $isNullable;
        } else {
            $column['type']     = $this->_resourceHelper->getDdlTypeByColumnType($attributeType);
            $column['nullable'] = $isNullable;
            $column['comment']  = 'Enterprise Giftcard Open ' . $attributeCode . ' column';
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
        return $this->_eavAttributeFactory->create()->getFlatUpdateSelect($this->getAttribute(), $store);
    }
}
