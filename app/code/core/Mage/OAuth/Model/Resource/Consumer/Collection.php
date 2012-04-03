<?php
/**
 * {license}
 *
 * @category    Mage
 * @package     Mage_OAuth
 */

/**
 * OAuth Application resource collection model
 *
 * @category    Mage
 * @package     Mage_OAuth
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_OAuth_Model_Resource_Consumer_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Initialize collection model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Mage_OAuth_Model_Consumer', 'Mage_OAuth_Model_Resource_Consumer');
    }
}
