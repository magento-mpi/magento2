<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sendfriend
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Sendfriend log resource collection
 *
 * @category    Magento
 * @package     Magento_Sendfriend
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Sendfriend_Model_Resource_Sendfriend_Collection extends Magento_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Init resource collection
     *
     */
    protected function _construct()
    {
        $this->_init('Magento_Sendfriend_Model_Sendfriend', 'Magento_Sendfriend_Model_Resource_Sendfriend');
    }
}
