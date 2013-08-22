<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Quotes collection
 *
 * @category    Magento
 * @package     Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Sales_Model_Resource_Quote_Collection extends Magento_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Resource initialization
     *
     */
    protected function _construct()
    {
        $this->_init('Magento_Sales_Model_Quote', 'Magento_Sales_Model_Resource_Quote');
    }
}

