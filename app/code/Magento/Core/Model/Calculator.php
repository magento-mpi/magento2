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
namespace Magento\Core\Model;

class Calculator
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
     * @var Store|null
     */
    protected $_store = null;

    /**
     * Initialize calculator
     *
     * @param Store|int $store
     * @param StoreManagerInterface $storeManager
     */
    public function __construct($store, StoreManagerInterface $storeManager)
    {
        if (!($store instanceof \Magento\Core\Model\Store)) {
            $store = $storeManager->getStore($store);
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
