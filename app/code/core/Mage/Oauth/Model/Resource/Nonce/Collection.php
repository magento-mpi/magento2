<?php
/**
 * {license}
 *
 * @category    Mage
 * @package     Mage_Oauth
 */

/**
 * OAuth nonce resource collection model
 *
 * @category    Mage
 * @package     Mage_Oauth
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Oauth_Model_Resource_Nonce_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Initialize collection model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Mage_Oauth_Model_Nonce', 'Mage_OAuth_Model_Resource_Nonce');
    }
}
