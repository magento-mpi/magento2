<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Tax
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @category    Mage
 * @package     Mage_Tax
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Tax_Model_Sales_Order_Tax_Item extends Magento_Core_Model_Abstract
{
    /**
     * Initialization
     */
    protected function _construct()
    {
        $this->_init('Mage_Tax_Model_Resource_Sales_Order_Tax_Item');
    }
}
