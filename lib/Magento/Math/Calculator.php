<?php
/**
 * Calculations library
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Math;

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
     * @var \Magento\BaseScopeInterface
     */
    protected $_scope = null;

    /**
     * Initialize calculator
     *
     * @param \Magento\BaseScopeInterface
     */
    public function __construct(\Magento\BaseScopeInterface $scope)
    {
        $this->_scope = $scope;
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
            $roundedPrice = $this->_scope->roundPrice($price);
            $this->_delta = $price - $roundedPrice;
            if ($negative) {
                $this->_delta = -$this->_delta;
            }
        }
        return $roundedPrice;
    }
}
