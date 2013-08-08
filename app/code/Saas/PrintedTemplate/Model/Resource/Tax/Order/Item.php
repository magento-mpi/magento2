<?php
/**
 * {license_notice}
 *
 * @category    Saas
 * @package     Saas_PrintedTemplate
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Tax information for order item
 *
 * @category    Saas
 * @package     Saas_PrintedTemplate
 * @subpackage  Models
 */
class Saas_PrintedTemplate_Model_Resource_Tax_Order_Item extends Magento_Core_Model_Resource_Db_Abstract
{
    /**
     * Resource model constructor.
     * Initialize connection to database and associated table.
     */
    protected function _construct()
    {
        $this->_init('saas_printed_template_order_item_tax', 'item_tax_id');
    }
}
