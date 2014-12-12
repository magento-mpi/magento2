<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\SalesArchive\Model\Resource\Order\Shipment;

/**
 * Order shipment archive collection
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Collection extends \Magento\Sales\Model\Resource\Order\Shipment\Grid\Collection
{
    /**
     * Collection initialization
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setMainTable('magento_sales_shipment_grid_archive');
    }
}
