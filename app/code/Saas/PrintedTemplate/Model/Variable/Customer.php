<?php
/**
 * {license_notice}
 *
 * @category   Saas
 * @package    Saas_PrintedTemplate
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Container for Magento_Sales_Model_Order for Customer variable
 *
 * Container that can restrict access to properties and method
 * with white list.
 *
 * @category   Saas
 * @package    Saas_PrintedTemplate
 * @subpackage Models
 */
class Saas_PrintedTemplate_Model_Variable_Customer extends Saas_PrintedTemplate_Model_Variable_Abstract
{
    /**
     * Constructor
     *
     * @see Saas_PrintedTemplate_Model_Template_Variable_Abstract::__construct()
     * @param Magento_Sales_Model_Order $value Order
     */
    public function __construct(Magento_Sales_Model_Order $value)
    {
        parent::__construct($value);
        $this->_setListsFromConfig('customer');
    }

    /**
     * Formats currency using order formater
     *
     * @param float
     * @return string
     */
    public function formatCurrency($value)
    {
        return (null !== $value) ? $this->_value->formatPriceTxt($value) : '';
    }

    /**
     * Formats currency using order formater
     *
     * @param float
     * @return string
     */
    public function formatBaseCurrency($value)
    {
        return $this->_value->formatBasePrice($value);
    }
}
