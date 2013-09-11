<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Flat sales order creditmemo items collection
 *
 * @category    Magento
 * @package     Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Sales\Model\Resource\Order\Creditmemo\Item;

class Collection extends \Magento\Sales\Model\Resource\Collection\AbstractCollection
{
    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix    = 'sales_order_creditmemo_item_collection';

    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject    = 'order_creditmemo_item_collection';

    /**
     * Model initialization
     *
     */
    protected function _construct()
    {
        $this->_init('\Magento\Sales\Model\Order\Creditmemo\Item', '\Magento\Sales\Model\Resource\Order\Creditmemo\Item');
    }

    /**
     * Set creditmemo filter
     *
     * @param int $creditmemoId
     * @return \Magento\Sales\Model\Resource\Order\Creditmemo\Item\Collection
     */
    public function setCreditmemoFilter($creditmemoId)
    {
        $this->addFieldToFilter('parent_id', $creditmemoId);
        return $this;
    }
}
