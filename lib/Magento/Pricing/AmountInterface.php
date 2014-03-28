<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Pricing;

/**
 * Amount interface
 */
interface AmountInterface
{
    /**
     * Returns amount
     *
     * @return float
     */
    public function getAmount();

    /**
     * Returns display amount
     *
     * @param null $excludedCode
     * @return float
     */
    public function getDisplayAmount($excludedCode = null);
}
