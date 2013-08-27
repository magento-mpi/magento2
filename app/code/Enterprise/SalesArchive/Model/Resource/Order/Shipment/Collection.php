<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_SalesArchive
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Order shipment archive collection
 *
 * @category    Enterprise
 * @package     Enterprise_SalesArchive
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_SalesArchive_Model_Resource_Order_Shipment_Collection
    extends Magento_Sales_Model_Resource_Order_Shipment_Grid_Collection
{
    /**
     * Collection initialization
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setMainTable('enterprise_sales_shipment_grid_archive');
    }
}
