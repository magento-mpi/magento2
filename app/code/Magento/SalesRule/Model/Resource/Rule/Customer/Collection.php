<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_SalesRule
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * SalesRule Model Resource Rule Customer_Collection
 *
 * @category    Magento
 * @package     Magento_SalesRule
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_SalesRule_Model_Resource_Rule_Customer_Collection extends Magento_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Collection constructor
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('Magento_SalesRule_Model_Rule_Customer', 'Magento_SalesRule_Model_Resource_Rule_Customer');
    }
}
