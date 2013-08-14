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
 * Tax infoirmation for order shipping
 *
 * @category    Saas
 * @package     Saas_PrintedTemplate
 * @subpackage  Models
 */
class Saas_PrintedTemplate_Model_Tax_Order_Shipping extends Magento_Core_Model_Abstract
{
    /**
     * Model constructor. Initialize resource for data.
     */
    protected function _construct()
    {
        $this->_init('Saas_PrintedTemplate_Model_Resource_Tax_Order_Shipping');
    }
}
