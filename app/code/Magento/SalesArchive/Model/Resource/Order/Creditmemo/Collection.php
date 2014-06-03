<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\SalesArchive\Model\Resource\Order\Creditmemo;

/**
 * Order creditmemo archive collection
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Collection extends \Magento\Sales\Model\Resource\Order\Creditmemo\Grid\Collection
{
    /**
     * Collection initialization
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setMainTable('magento_sales_creditmemo_grid_archive');
    }
}
