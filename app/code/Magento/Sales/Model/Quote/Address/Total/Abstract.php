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
 * Sales Quote Address Total  abstract model
 */
abstract class Magento_Sales_Model_Quote_Address_Total_Abstract
{
    /**
     * Total Code name
     *
     * @var string
     */
    protected $_code;
    protected $_address = null;

    /**
     * Various abstract abilities
     * @var bool
     */
    protected $_canAddAmountToAddress = true;
    protected $_canSetAddressAmount   = true;

    /**
     * Key for item row total getting
     *
     * @var string
     */
    protected $_itemRowTotalKey = null;

    /**
     * Set total code code name
     *
     * @param string $code
     * @return Magento_Sales_Model_Quote_Address_Total_Abstract
     */
    public function setCode($code)
    {
        $this->_code = $code;
        return $this;
    }

    /**
     * Retrieve total code name
     *
     * @return string
     */
    public function getCode()
    {
        return $this->_code;
    }

    /**
     * Label getter
     *
     * @return string
     */
    public function getLabel()
    {
        return '';
    }

    /**
     * Collect totals process.
     *
     * @param Magento_Sales_Model_Quote_Address $address
     * @return Magento_Sales_Model_Quote_Address_Total_Abstract
     */
    public function collect(Magento_Sales_Model_Quote_Address $address)
    {
        $this->_setAddress($address);
        /**
         * Reset amounts
         */
        $this->_setAmount(0);
        $this->_setBaseAmount(0);
        return $this;
    }

    /**
     * Fetch (Retrieve data as array)
     *
     * @param Magento_Sales_Model_Quote_Address $address
     * @return array
     */
    public function fetch(Magento_Sales_Model_Quote_Address $address)
    {
        $this->_setAddress($address);
        return array();
    }

    /**
     * Set address shich can be used inside totals calculation
     *
     * @param   Magento_Sales_Model_Quote_Address $address
     * @return  Magento_Sales_Model_Quote_Address_Total_Abstract
     */
    protected function _setAddress(Magento_Sales_Model_Quote_Address $address)
    {
        $this->_address = $address;
        return $this;
    }

    /**
     * Get quote address object
     *
     * @return  Magento_Sales_Model_Quote_Address
     * @throws   Magento_Core_Exception if address not declared
     */
    protected function _getAddress()
    {
        if ($this->_address === null) {
            throw new Magento_Core_Exception(
                __('The address model is not defined.')
            );
        }
        return $this->_address;
    }

    /**
     * Set total model amount value to address
     *
     * @param   float $amount
     * @return  Magento_Sales_Model_Quote_Address_Total_Abstract
     */
    protected function _setAmount($amount)
    {
        if ($this->_canSetAddressAmount) {
            $this->_getAddress()->setTotalAmount($this->getCode(), $amount);
        }
        return $this;
    }

    /**
     * Set total model base amount value to address
     *
     * @param float $baseAmount
     * @internal param float $amount
     * @return  Magento_Sales_Model_Quote_Address_Total_Abstract
     */
    protected function _setBaseAmount($baseAmount)
    {
        if ($this->_canSetAddressAmount) {
            $this->_getAddress()->setBaseTotalAmount($this->getCode(), $baseAmount);
        }
        return $this;
    }

    /**
     * Add total model amount value to address
     *
     * @param   float $amount
     * @return  Magento_Sales_Model_Quote_Address_Total_Abstract
     */
    protected function _addAmount($amount)
    {
        if ($this->_canAddAmountToAddress) {
            $this->_getAddress()->addTotalAmount($this->getCode(),$amount);
        }
        return $this;
    }

    /**
     * Add total model base amount value to address
     *
     * @param float $baseAmount
     * @return  Magento_Sales_Model_Quote_Address_Total_Abstract
     */
    protected function _addBaseAmount($baseAmount)
    {
        if ($this->_canAddAmountToAddress) {
            $this->_getAddress()->addBaseTotalAmount($this->getCode(), $baseAmount);
        }
        return $this;
    }

    /**
     * Get all items except nominals
     *
     * @param Magento_Sales_Model_Quote_Address $address
     * @return array
     */
    protected function _getAddressItems(Magento_Sales_Model_Quote_Address $address)
    {
        return $address->getAllNonNominalItems();
    }

    /**
     * Getter for row default total
     *
     * @param Magento_Sales_Model_Quote_Item_Abstract $item
     * @return float
     */
    public function getItemRowTotal(Magento_Sales_Model_Quote_Item_Abstract $item)
    {
        if (!$this->_itemRowTotalKey) {
            return 0;
        }
        return $item->getDataUsingMethod($this->_itemRowTotalKey);
    }

    /**
     * Getter for row default base total
     *
     * @param Magento_Sales_Model_Quote_Item_Abstract $item
     * @return float
     */
    public function getItemBaseRowTotal(Magento_Sales_Model_Quote_Item_Abstract $item)
    {
        if (!$this->_itemRowTotalKey) {
            return 0;
        }
        return $item->getDataUsingMethod('base_' . $this->_itemRowTotalKey);
    }

    /**
     * Whether the item row total may be compouded with others
     *
     * @param Magento_Sales_Model_Quote_Item_Abstract $item
     * @return bool
     */
    public function getIsItemRowTotalCompoundable(Magento_Sales_Model_Quote_Item_Abstract $item)
    {
        if ($item->getData("skip_compound_{$this->_itemRowTotalKey}")) {
            return false;
        }
        return true;
    }

    /**
     * Process model configuration array.
     * This method can be used for changing models apply sort order
     *
     * @param   array $config
     * @param   store $store
     * @return  array
     */
    public function processConfigArray($config, $store)
    {
        return $config;
    }
}
