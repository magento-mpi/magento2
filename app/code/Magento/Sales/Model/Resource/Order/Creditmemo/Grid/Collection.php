<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Flat sales order creditmemo grid collection
 *
 * @category    Magento
 * @package     Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Sales_Model_Resource_Order_Creditmemo_Grid_Collection
    extends Magento_Sales_Model_Resource_Order_Creditmemo_Collection
{
    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix    = 'sales_order_creditmemo_grid_collection';

    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject    = 'order_creditmemo_grid_collection';

    /**
     * Model initialization
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setMainTable('sales_flat_creditmemo_grid');
    }
}
