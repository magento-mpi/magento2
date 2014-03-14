<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Model\Resource\Order\Creditmemo;

/**
 * Flat sales order creditmemo item resource
 *
 * @category    Magento
 * @package     Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Item extends \Magento\Sales\Model\Resource\Order\AbstractOrder
{
    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix    = 'sales_order_creditmemo_item_resource';

    /**
     * Model initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('sales_flat_creditmemo_item', 'entity_id');
    }
}
