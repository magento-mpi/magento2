<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Eav
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Entity attribute option collection
 *
 * @category    Mage
 * @package     Mage_Eav
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Eav_Model_Resource_Entity_Attribute_Option_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Option value table
     *
     * @var string
     */
    protected $_optionValueTable;

    /**
     * Resource initialization
     */
    protected function _construct()
    {
        $this->_init('Mage_Eav_Model_Entity_Attribute_Option', 'Mage_Eav_Model_Resource_Entity_Attribute_Option');
        $this->_optionValueTable = Mage::getSingleton('Mage_Core_Model_Resource')->getTableName('eav_attribute_option_value');
    }

    /**
     * Set attribute filter
     *
     * @param int $setId
     * @return Mage_Eav_Model_Resource_Entity_Attribute_Option_Collection
     */
    public function setAttributeFilter($setId)
    {
        return $this->addFieldToFilter('attribute_id', $setId);
    }


    /**
     * Add store filter to collection
     *
     * @param int $storeId
     * @param bolean $useDefaultValue
     * @return Mage_Eav_Model_Resource_Entity_Attribute_Option_Collection
     */
    public function setStoreFilter($storeId = null, $useDefaultValue = true)
    {
        if (is_null($storeId)) {
            $storeId = Mage::app()->getStore()->getId();
        }
        $adapter = $this->getConnection();

        $joinCondition = $adapter->quoteInto('tsv.option_id = main_table.option_id AND tsv.store_id = ?', $storeId);

        if ($useDefaultValue) {
            $this->getSelect()
                ->join(
                    array('tdv' => $this->_optionValueTable),
                    'tdv.option_id = main_table.option_id',
                    array('default_value' => 'value'))
                ->joinLeft(
                    array('tsv' => $this->_optionValueTable),
                    $joinCondition,
                    array(
                        'store_default_value' => 'value',
                        'value'               => $adapter->getCheckSql('tsv.value_id > 0', 'tsv.value', 'tdv.value')
                    ))
                ->where('tdv.store_id = ?', 0);
        } else {
            $this->getSelect()
                ->joinLeft(
                    array('tsv' => $this->_optionValueTable),
                    $joinCondition,
                    'value')
                ->where('tsv.store_id = ?', $storeId);
        }

        $this->setOrder('tsv.value', self::SORT_ORDER_ASC);

        return $this;
    }

    /**
     * Add option id(s) frilter to collection
     *
     * @param int|array $optionId
     * @return Mage_Eav_Model_Resource_Entity_Attribute_Option_Collection
     */
    public function setIdFilter($optionId)
    {
        return $this->addFieldToFilter('option_id', array('in' => $optionId));
    }

    /**
     * Convert collection items to select options array
     *
     * @param string $valueKey
     * @return array
     */
    public function toOptionArray($valueKey = 'value')
    {
        return $this->_toOptionArray('option_id', $valueKey);
    }


    /**
     * Set order by position or alphabetically by values in admin
     *
     * @param string $dir direction
     * @param boolean $sortAlpha sort alphabetically by values in admin
     * @return Mage_Eav_Model_Resource_Entity_Attribute_Option_Collection
     */
    public function setPositionOrder($dir = self::SORT_ORDER_ASC, $sortAlpha = false)
    {
        $this->setOrder('main_table.sort_order', $dir);
        // sort alphabetically by values in admin
        if ($sortAlpha) {
            $this->getSelect()
                ->joinLeft(
                    array('sort_alpha_value' => $this->_optionValueTable),
                    'sort_alpha_value.option_id = main_table.option_id AND sort_alpha_value.store_id = 0',
                    array('value'));
            $this->setOrder('sort_alpha_value.value', $dir);
        }

        return $this;
    }
}
