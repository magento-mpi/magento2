<?php
/**
 * {license_notice}
 *
 * @category    Saas
 * @package     Saas_UnitPrice
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * UnitPrice model
 *
 * @category   Saas
 * @package    UnitPrice
 */
class Saas_UnitPrice_Model_Unitprice
{
    /**
     * Reference unit
     *
     * @var string
     */
    private $_referenceUnit;

    /**
     * Reference amount
     *
     * @var float
     */
    private $_referenceAmount;

    /**
     * Creates model
     *
     * @param array $params
     */
    public function __construct(array $params = array())
    {
        if (isset($params['reference_unit'])) {
            $this->_referenceUnit = $params['reference_unit'];
        }
        if (isset($params['reference_amount'])) {
            $this->_referenceAmount = $params['reference_amount'];
        }
    }

    /**
     * Returns reference unit
     *
     * @throws Mage_Core_Exception if unit was not passed to constructor
     * @return string
     */
    protected function _getReferenceUnit()
    {
        if (!$this->_referenceUnit) {
            $this->_throwMageException(__('Reference unit not set'));
        }

        return $this->_referenceUnit;
    }

    /**
     * Returns reference amount; loads default one from config if
     * was nto passed to constructor.
     *
     * @return string
     */
    protected function _getReferenceAmount()
    {
        if (!$this->_referenceAmount) {
            $this->_referenceAmount = $this->_getHelper()->getConfig('default_unit_price_base_amount');
        }

        return $this->_referenceAmount;
    }

    /**
     * Calculates price of reference amount of reference unit of product
     * by given amount, unit and price.
     *
     * @param float $productAmount Amount (positive real value)
     * @param string $productUnit Unit
     * @param price $productPrice Price
     * @throws Mage_Core_Exception On incorrect arguments
     * @return float Unit price
     */
    public function getUnitPrice($productAmount, $productUnit, $productPrice)
    {
        if ($productAmount <= 0) {
            $this->_throwMageException(
                __('The product unit amount must be greater than zero')
            );
        }

        $rate   = $this->getConversionRate($productUnit, $this->_getReferenceUnit());
        $result = $productPrice / $productAmount / $rate * $this->_getReferenceAmount();

        return $result;
    }

    /**
     * Returns the conversion rate from one unit to another
     *
     * @param string $fromUnit Coverted from
     * @param string $toUnit Converted to
     * @return float Rate
     */
    public function getConversionRate($fromUnit, $toUnit)
    {
        $fromUnit = trim(strtoupper($fromUnit));
        $toUnit   = trim(strtoupper($toUnit));

        if ($fromUnit == $toUnit) {
            return 1;
        }

        $rate = $this->_getHelper()->getConfig("convert/$fromUnit/to/$toUnit");
        $helper = $this->_getHelper();

        if (!$rate) {
            $this->_throwMageException(
                __('Conversion rate not found for %s to %s', __($fromUnit), __($toUnit))
            );
        }

        return $rate;
    }

    /**
     * Throws Mage_Core_Exception
     * (for unit tests)
     *
     * @param string $message Message
     * @throws Mage_Core_Exception
     */
    protected function _throwMageException($message)
    {
        Mage::throwException($message);
    }

    /**
     * Returns Saas_UnitPrice_Helper_Data
     * (for unit tests)
     *
     * @return Saas_UnitPrice_Helper_Data
     */
    protected function _getHelper()
    {
        return Mage::helper('Saas_UnitPrice_Helper_Data');
    }
}
