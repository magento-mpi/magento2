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
 * Order Tax Item Collection
 *
 * @category    Mage
 * @package     Mage_Tax
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Tax_Model_Resource_Sales_Order_Tax_Item_Collection extends Magento_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Initialize resource
     */
    protected function _construct()
    {
        $this->_init('Mage_Tax_Model_Sales_Order_Tax_Item', 'Mage_Tax_Model_Resource_Sales_Order_Tax_Item');
    }
}
