<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Model\Resource\Order\Invoice\Grid;

/**
 * Flat sales order invoice grid collection
 *
 * @category    Magento
 * @package     Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Collection extends \Magento\Sales\Model\Resource\Order\Invoice\Collection
{
    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix    = 'sales_order_invoice_grid_collection';

    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject    = 'order_invoice_grid_collection';

    /**
     * Model initialization
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setMainTable('sales_flat_invoice_grid');
    }
}
