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
namespace Magento\Sales\Model\Resource\Order\Payment;

class Collection extends \Magento\Sales\Model\Resource\Order\Collection\AbstractCollection
{
    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix    = 'sales_order_payment_collection';

    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject    = 'order_payment_collection';

    /**
     * Model initialization
     *
     */
    protected function _construct()
    {
        $this->_init('\Magento\Sales\Model\Order\Payment', '\Magento\Sales\Model\Resource\Order\Payment');
    }

    /**
     * Unserialize additional_information in each item
     *
     * @return \Magento\Sales\Model\Resource\Order\Payment\Collection
     */
    protected function _afterLoad()
    {
        foreach ($this->_items as $item) {
            $this->getResource()->unserializeFields($item);
        }

        /** @var \Magento\Sales\Model\Order\Payment $item */
        foreach ($this->_items as $item) {
            foreach ($item->getData() as $fieldName => $fieldValue) {
                $item->setData($fieldName,
                    \Mage::getSingleton('Magento\Sales\Model\Payment\Method\Converter')->decode($item, $fieldName)
                );
            }
        }

        return parent::_afterLoad();
    }
}
