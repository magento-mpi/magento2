<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Quote addresses shiping rates collection
 *
 * @category    Mage
 * @package     Mage_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Model_Resource_Quote_Address_Rate_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
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
        $this->_init('Mage_Sales_Model_Quote_Address_Rate', 'Mage_Sales_Model_Resource_Quote_Address_Rate');
    }

    /**
     * Set filter by address id
     *
     * @param int $addressId
     * @return Mage_Sales_Model_Resource_Quote_Address_Rate_Collection
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
     * @return Mage_Sales_Model_Resource_Quote_Address_Rate_Collection
     */
    public function setFixedOnlyFilter($value)
    {
        $this->_allowFixedOnly = (bool)$value;
        return $this;
    }

    /**
     * Don't add item to the collection if only fixed are allowed and its carrier is not fixed
     *
     * @param Mage_Sales_Model_Quote_Address_Rate $rate
     * @return Mage_Sales_Model_Resource_Quote_Address_Rate_Collection
     */
    public function addItem(Magento_Object $rate)
    {
        if ($this->_allowFixedOnly && (!$rate->getCarrierInstance() || !$rate->getCarrierInstance()->isFixed())) {
            return $this;
        }
        return parent::addItem($rate);
    }
}
