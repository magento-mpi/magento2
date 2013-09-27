<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftCard
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_GiftCard_Model_Source_Type extends Magento_Eav_Model_Entity_Attribute_Source_Abstract
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
     * @param Magento_Eav_Model_Resource_Entity_AttributeFactory $eavAttributeFactory
     * @param Magento_Core_Helper_Data $coreData
     */
    public function __construct(
        Magento_Eav_Model_Resource_Entity_AttributeFactory $eavAttributeFactory,
        Magento_Core_Helper_Data $coreData
    ) {
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
            Magento_GiftCard_Model_Giftcard::TYPE_VIRTUAL  => __('Virtual'),
            Magento_GiftCard_Model_Giftcard::TYPE_PHYSICAL => __('Physical'),
            Magento_GiftCard_Model_Giftcard::TYPE_COMBINED => __('Combined'),
        );
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
            $column['type']     = 'tinyint';
            $column['is_null']  = true;
        } else {
            $column['type']     = Magento_DB_Ddl_Table::TYPE_SMALLINT;
            $column['nullable'] = true;
            $column['comment']  = 'Enterprise Giftcard Type ' . $attributeCode . ' column';
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
