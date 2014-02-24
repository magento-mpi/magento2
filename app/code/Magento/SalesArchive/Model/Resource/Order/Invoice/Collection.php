<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_SalesArchive
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\SalesArchive\Model\Resource\Order\Invoice;

/**
 * Order invoice archive collection
 *
 * @category    Magento
 * @package     Magento_SalesArchive
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Collection
    extends \Magento\Sales\Model\Resource\Order\Invoice\Grid\Collection
{
    /**
     * Collection initialization
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setMainTable('magento_sales_invoice_grid_archive');
    }
}
