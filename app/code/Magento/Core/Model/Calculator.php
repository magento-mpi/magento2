<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Calculations model
 *
 * @category    Magento
 * @package     Magento_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Core_Model_Calculator
{
    /**
     * Delta collected during rounding steps
     *
     * @var float
     */
    protected $_delta = 0.0;

    /**
     * Store instance
     *
     * @var Magento_Core_Model_Store|null
     */
    protected $_store = null;

    /**
     * Initialize calculator
     *
     * @param Magento_Core_Model_Store|int $store
     */
    public function __construct($store)
    {
        if (!($store instanceof Magento_Core_Model_Store)) {
            $store = Mage::app()->getStore($store);
        }
        $this->_store = $store;
    }

    /**
     * Round price considering delta
     *
     * @param float $price
     * @param bool $negative Indicates if we perform addition (true) or subtraction (false) of rounded value
     * @return float
     */
    public function deltaRound($price, $negative = false)
    {
        $roundedPrice = $price;
        if ($roundedPrice) {
            if ($negative) {
                $this->_delta = -$this->_delta;
            }
            $price  += $this->_delta;
            $roundedPrice = $this->_store->roundPrice($price);
            $this->_delta = $price - $roundedPrice;
            if ($negative) {
                $this->_delta = -$this->_delta;
            }
        }
        return $roundedPrice;
    }
}
