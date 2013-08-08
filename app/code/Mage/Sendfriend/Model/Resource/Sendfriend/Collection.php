<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Sendfriend
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Sendfriend log resource collection
 *
 * @category    Mage
 * @package     Mage_Sendfriend
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sendfriend_Model_Resource_Sendfriend_Collection extends Magento_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Init resource collection
     *
     */
    protected function _construct()
    {
        $this->_init('Mage_Sendfriend_Model_Sendfriend', 'Mage_Sendfriend_Model_Resource_Sendfriend');
    }
}
