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
 * Quote addresses shiping rates collection
 *
 * @category    Magento
 * @package     Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Sales\Model\Resource\Quote\Address\Rate;

class Collection extends \Magento\Core\Model\Resource\Db\Collection\AbstractCollection
{
    /**
     * Whether to load fixed items only
     *
     * @var bool
     */
    protected $_allowFixedOnly   = false;

    /**
     * Resource initialization
     *
     */
    protected function _construct()
    {
        $this->_init('Magento\Sales\Model\Quote\Address\Rate', 'Magento\Sales\Model\Resource\Quote\Address\Rate');
    }

    /**
     * Set filter by address id
     *
     * @param int $addressId
     * @return \Magento\Sales\Model\Resource\Quote\Address\Rate\Collection
     */
    public function setAddressFilter($addressId)
    {
        if ($addressId) {
            $this->addFieldToFilter('address_id', $addressId);
        } else {
            $this->_totalRecords = 0;
            $this->_setIsLoaded(true);
        }
        return $this;
    }

    /**
     * Setter for loading fixed items only
     *
     * @param bool $value
     * @return \Magento\Sales\Model\Resource\Quote\Address\Rate\Collection
     */
    public function setFixedOnlyFilter($value)
    {
        $this->_allowFixedOnly = (bool)$value;
        return $this;
    }

    /**
     * Don't add item to the collection if only fixed are allowed and its carrier is not fixed
     *
     * @param \Magento\Sales\Model\Quote\Address\Rate $rate
     * @return \Magento\Sales\Model\Resource\Quote\Address\Rate\Collection
     */
    public function addItem(\Magento\Object $rate)
    {
        if ($this->_allowFixedOnly && (!$rate->getCarrierInstance() || !$rate->getCarrierInstance()->isFixed())) {
            return $this;
        }
        return parent::addItem($rate);
    }
}
