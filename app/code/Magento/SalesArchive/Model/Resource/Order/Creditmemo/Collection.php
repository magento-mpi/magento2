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
namespace Magento\SalesArchive\Model\Resource\Order\Creditmemo;

class Collection
    extends \Magento\Sales\Model\Resource\Order\Creditmemo\Grid\Collection
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
