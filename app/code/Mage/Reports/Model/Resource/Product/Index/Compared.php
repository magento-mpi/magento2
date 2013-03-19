<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Reports
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Reports Compared Product Index Resource Model
 *
 * @category    Mage
 * @package     Mage_Reports
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Reports_Model_Resource_Product_Index_Compared extends Mage_Reports_Model_Resource_Product_Index_Abstract
{
    /**
     * Initialize connection and main resource table
     *
     */
    protected function _construct()
    {
        $this->_init('report_compared_product_index', 'index_id');
    }
}
