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
 * Container for Magento_Sales_Model_Order_Address for address variable
 *
 * Container that can restrict access to properties and method
 * with white list.
 *
 * @category   Saas
 * @package    Saas_PrintedTemplate
 * @subpackage Models
 */
class Saas_PrintedTemplate_Model_Variable_Address_Shipping
    extends Saas_PrintedTemplate_Model_Variable_Address_Abstract
{
    /**
     * Initializes model
     *
     * @param Magento_Sales_Model_Order_Address $value
     */
    public function __construct(Magento_Sales_Model_Order_Address $value)
    {
        parent::__construct($value);
        $this->_setListsFromConfig('address_shipping');
    }
}
