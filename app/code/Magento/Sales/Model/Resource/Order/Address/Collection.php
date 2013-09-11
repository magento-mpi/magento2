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
 * Flat sales order payment collection
 *
 * @category    Magento
 * @package     Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Sales\Model\Resource\Order\Address;

class Collection extends \Magento\Sales\Model\Resource\Order\Collection\AbstractCollection
{
    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix    = 'sales_order_address_collection';

    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject    = 'order_address_collection';

    /**
     * Model initialization
     *
     */
    protected function _construct()
    {
        $this->_init('Magento\Sales\Model\Order\Address', 'Magento\Sales\Model\Resource\Order\Address');
    }

    /**
     * Redeclare after load method for dispatch event
     *
     * @return \Magento\Sales\Model\Resource\Order\Address\Collection
     */
    protected function _afterLoad()
    {
        parent::_afterLoad();

        \Mage::dispatchEvent($this->_eventPrefix . '_load_after', array(
            $this->_eventObject => $this
        ));

        return $this;
    }
}
