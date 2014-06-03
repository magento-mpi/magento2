<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tax\Model\Resource\Sales\Order;

/**
 * Sales order tax resource model
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Tax extends \Magento\Framework\Model\Resource\Db\AbstractDb
{
    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('sales_order_tax', 'tax_id');
    }
}
