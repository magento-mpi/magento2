<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Sales report resource model
 *
 * @category    Mage
 * @package     Mage_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Model_Resource_Report extends Magento_Core_Model_Resource_Db_Abstract
{
    /**
     * Resource initialization
     *
     */
    protected function _construct()
    {
        
    }

    /**
     * Set main table and idField
     *
     * @param string $table
     * @param string $field
     * @return Mage_Sales_Model_Resource_Report
     */
    public function init($table, $field = 'id')
    {
        $this->_init($table, $field);
        return $this;
    }
}
