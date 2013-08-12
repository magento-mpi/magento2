<?php
/**
 * {license_notice}
 *
 * @category    Saas
 * @package     Saas_UnitPrice
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Source model for unitprice attributes
 *
 * @category   Saas
 * @package    Saas_UnitPrice
 */
class Saas_UnitPrice_Model_Entity_Source_Unitprice_Unit
    extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
    protected $_unitPriceDefaultKey = '';

    protected $_options;

    /**
     * Get options array
     *
     * @return array
     */
    public function toOptionArray()
    {
        if (!$this->_options) {
            $this->_options = $this->_getUnitConfigSourceModel()->toOptionArray();
        }

        return $this->_options;
    }

    /**
     * Get options array
     *
     * @return array
     */
    public function getAllOptions()
    {
        return $this->toOptionArray();
    }

    /**
     * Get attribute default value from configuration
     *
     * @return string
     */
    public function getDefaultValue()
    {
        return $this->_getHelper()->getConfig(
            'default_' . $this->getAttribute()->getAttributeCode()
        );
    }

    /**
     * Get attribute column settings
     *
     * @return array
     */
    public function getFlatColums()
    {
        return array($this->getAttribute()->getAttributeCode() => array(
          'type'      => 'varchar(255)',
          'unsigned'  => false,
          'is_null'   => true,
          'default'   => $this->getDefaultValue(),
          'extra'     => null
        ));
    }

    /**
     * Retrieve Select For Flat Attribute update
     *
     * @param int $store
     * @return Magento_DB_Select|null
     */
    public function getFlatUpdateSelect($store)
    {
        $attribute = $this->getAttribute();
        $joinCondition = "`e`.`entity_id`=`t1`.`entity_id`";
        if ($attribute->getFlatAddChildData()) {
            $joinCondition .= " AND `e`.`child_id`=`t1`.`entity_id`";
        }
        $select = $attribute->getResource()->getReadConnection()->select()
            ->joinLeft(array('t1' => $attribute->getBackend()->getTable()),
                $joinCondition,
                array()
            )
            ->joinLeft(
                array('t2' => $attribute->getBackend()->getTable()),
                "t2.entity_id = t1.entity_id"
                . " AND t1.entity_type_id = t2.entity_type_id"
                . " AND t1.attribute_id = t2.attribute_id"
                . " AND t2.store_id = {$store}",
                array($attribute->getAttributeCode() => "IFNULL(t2.value, t1.value)")
            )
            ->where("t1.entity_type_id=?", $attribute->getEntityTypeId())
            ->where("t1.attribute_id=?", $attribute->getId())
            ->where("t1.store_id=?", 0);

        if ($attribute->getFlatAddChildData()) {
            $select->where("e.is_child=?", 0);
        }
        return $select;
    }

    /**
     * Get UnitPrice data helper
     *
     * @return Saas_UnitPrice_Helper_Data
     */
    protected function _getHelper()
    {
        return Mage::helper('Saas_UnitPrice_Helper_Data');
    }

    /**
     * Get Unit Configuration source model
     *
     * @return Saas_UnitPrice_Model_Config_Source_Unitprice_Unit
     */
    protected function _getUnitConfigSourceModel()
    {
        return Mage::getModel('Saas_UnitPrice_Model_Config_Source_Unitprice_Unit');
    }
}
