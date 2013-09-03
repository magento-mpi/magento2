<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Reports
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Reports Compared Product Index Resource Model
 *
 * @category    Magento
 * @package     Magento_Reports
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Reports_Model_Resource_Product_Index_Compared extends Magento_Reports_Model_Resource_Product_Index_Abstract
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
