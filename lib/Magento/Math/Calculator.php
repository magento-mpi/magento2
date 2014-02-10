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
     * @var \Magento\Core\Model\Store|null
     */
    protected $_scope = null;

    /**
     * Initialize calculator
     *
     * @param \Magento\BaseScopeResolverInterface $scopeResolver
     * @param \Magento\BaseScopeInterface|int $scope
     */
    public function __construct(\Magento\BaseScopeResolverInterface $scopeResolver, $scope = null)
    {
        if (!($scope instanceof \Magento\BaseScopeInterface)) {
            $scope = $scopeResolver->getScope($scope);
        }
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
