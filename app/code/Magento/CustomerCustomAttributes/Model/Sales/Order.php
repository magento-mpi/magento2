<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CustomerCustomAttributes
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Customer Order model
 *
 * @method \Magento\CustomerCustomAttributes\Model\Resource\Sales\Order _getResource()
 * @method \Magento\CustomerCustomAttributes\Model\Resource\Sales\Order getResource()
 * @method \Magento\CustomerCustomAttributes\Model\Sales\Order setEntityId(int $value)
 *
 * @category    Magento
 * @package     Magento_CustomerCustomAttributes
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\CustomerCustomAttributes\Model\Sales;

class Order extends \Magento\CustomerCustomAttributes\Model\Sales\AbstractSales
{
    /**
     * Initialize resource model
     *
     */
    protected function _construct()
    {
        $this->_init('Magento\CustomerCustomAttributes\Model\Resource\Sales\Order');
    }
}
