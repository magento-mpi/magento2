<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_SalesArchive
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Order creditmemo archive collection
 *
 * @category    Magento
 * @package     Magento_SalesArchive
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_SalesArchive_Model_Resource_Order_Creditmemo_Collection
    extends Magento_Sales_Model_Resource_Order_Creditmemo_Grid_Collection
{
    /**
     * Collection initialization
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setMainTable('magento_sales_creditmemo_grid_archive');
    }
}
