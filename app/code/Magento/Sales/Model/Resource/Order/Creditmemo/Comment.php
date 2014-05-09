<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Model\Resource\Order\Creditmemo;

/**
 * Flat sales order creditmemo comment resource
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Comment extends \Magento\Sales\Model\Resource\Order\AbstractOrder
{
    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'sales_order_creditmemo_comment_resource';

    /**
     * Model initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('sales_flat_creditmemo_comment', 'entity_id');
    }
}
