<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_CatalogRule
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * CatalogRule data helper
 */
class Mage_CatalogRule_Helper_Data extends Magento_Core_Helper_Abstract
{
    /**
     * Algorithm for calculating price rule
     *
     * @param  string $actionOperator
     * @param  int $ruleAmount
     * @param  float $price
     * @return float|int
     */
    public function calcPriceRule($actionOperator, $ruleAmount, $price)
    {
        $priceRule = 0;
        switch ($actionOperator) {
            case 'to_fixed':
                $priceRule = min($ruleAmount, $price);
                break;
            case 'to_percent':
                $priceRule = $price * $ruleAmount / 100;
                break;
            case 'by_fixed':
                $priceRule = max(0, $price - $ruleAmount);
                break;
            case 'by_percent':
                $priceRule = $price * (1 - $ruleAmount / 100);
                break;
        }
        return $priceRule;
    }
}
