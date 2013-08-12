<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Tax
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @category    Magento
 * @package     Magento_Tax
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Tax_Model_Sales_Order_Tax_Item extends Magento_Core_Model_Abstract
{
    /**
     * Initialization
     */
    protected function _construct()
    {
        $this->_init('Magento_Tax_Model_Resource_Sales_Order_Tax_Item');
    }
}
