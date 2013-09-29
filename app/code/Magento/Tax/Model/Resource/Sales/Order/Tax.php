<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Tax
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Sales order tax resource model
 *
 * @category    Magento
 * @package     Magento_Tax
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Tax\Model\Resource\Sales\Order;

class Tax extends \Magento\Core\Model\Resource\Db\AbstractDb
{
    /**
     * Resource initialization
     */
    protected function _construct()
    {
        $this->_init('sales_order_tax', 'tax_id');
    }
}
