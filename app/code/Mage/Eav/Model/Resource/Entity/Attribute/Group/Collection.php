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
 * Eav attribute group resource collection
 *
 * @category    Mage
 * @package     Mage_Eav
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Eav_Model_Resource_Entity_Attribute_Group_Collection extends Magento_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Init resource model for collection
     *
     */
    protected function _construct()
    {
        $this->_init('Mage_Eav_Model_Entity_Attribute_Group', 'Mage_Eav_Model_Resource_Entity_Attribute_Group');
    }

    /**
     * Set Attribute Set Filter
     *
     * @param int $setId
     * @return Mage_Eav_Model_Resource_Entity_Attribute_Group_Collection
     */
    public function setAttributeSetFilter($setId)
    {
        $this->addFieldToFilter('attribute_set_id', array('eq' => $setId));
        $this->setOrder('sort_order');
        return $this;
    }

    /**
     * Set sort order
     *
     * @param string $direction
     * @return Mage_Eav_Model_Resource_Entity_Attribute_Group_Collection
     */
    public function setSortOrder($direction = self::SORT_ORDER_ASC)
    {
        return $this->addOrder('sort_order', $direction);
    }
}
