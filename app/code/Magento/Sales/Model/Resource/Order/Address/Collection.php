<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Sales\Model\Resource\Order\Address;

/**
 * Flat sales order payment collection
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Collection extends \Magento\Sales\Model\Resource\Order\Collection\AbstractCollection
{
    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'sales_order_address_collection';

    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject = 'order_address_collection';

    /**
     * Model initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magento\Sales\Model\Order\Address', 'Magento\Sales\Model\Resource\Order\Address');
    }

    /**
     * Redeclare after load method for dispatch event
     *
     * @return $this
     */
    protected function _afterLoad()
    {
        parent::_afterLoad();

        $this->_eventManager->dispatch($this->_eventPrefix . '_load_after', [$this->_eventObject => $this]);

        return $this;
    }
}
