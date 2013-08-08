<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Tax
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Tax class collection
 *
 * @category    Mage
 * @package     Mage_Tax
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Tax_Model_Resource_Class_Collection extends Magento_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Resource initialization
     */
    public function _construct()
    {
        $this->_init('Mage_Tax_Model_Class', 'Mage_Tax_Model_Resource_Class');
    }

    /**
     * Add class type filter to result
     *
     * @param int $classTypeId
     * @return Mage_Tax_Model_Resource_Class_Collection
     */
    public function setClassTypeFilter($classTypeId)
    {
        return $this->addFieldToFilter('main_table.class_type', $classTypeId);
    }

    /**
     * Retrieve option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_toOptionArray('class_id', 'class_name');
    }

    /**
     * Retrieve option hash
     *
     * @return array
     */
    public function toOptionHash()
    {
        return $this->_toOptionHash('class_id', 'class_name');
    }
}
