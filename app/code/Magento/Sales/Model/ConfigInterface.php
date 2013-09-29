<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

interface Magento_Sales_Model_ConfigInterface
{
    /**
     * Retrieve renderer for area from config
     *
     * @param string $section
     * @param string $group
     * @param string $code
     * @param string $area
     * @return array
     */
    public function getTotalsRenderer($section, $group, $code, $area);

    /**
     * Retrieve totals for group
     * e.g. quote, nominal_totals, etc
     *
     * @param string $section
     * @param string $group
     * @return array
     */
    public function getGroupTotals($section, $group);

    /**
     * Get available product types
     *
     * @return array
     */
    public function getAvailableProductTypes();
}