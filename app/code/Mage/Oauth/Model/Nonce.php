<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Oauth
 * @copyright  {copyright}
 * @license    {license_link}
 */


/**
 * oAuth nonce model
 *
 * @category    Mage
 * @package     Mage_Oauth
 * @author      Magento Core Team <core@magentocommerce.com>
 * @method string getNonce()
 * @method Mage_Oauth_Model_Nonce setNonce() setNonce(string $nonce)
 * @method string getTimestamp()
 * @method Mage_Oauth_Model_Nonce setTimestamp() setTimestamp(string $timestamp)
 * @method Mage_Oauth_Model_Resource_Nonce getResource()
 * @method Mage_Oauth_Model_Resource_Nonce _getResource()
 */
class Mage_Oauth_Model_Nonce extends Magento_Core_Model_Abstract
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Mage_Oauth_Model_Resource_Nonce');
    }

    /**
     * "After save" actions
     *
     * @return Mage_Oauth_Model_Nonce
     */
    protected function _afterSave()
    {
        parent::_afterSave();

        //Cleanup old entries
        /** @var $helper Mage_Oauth_Helper_Data */
        $helper = Mage::helper('Mage_Oauth_Helper_Data');
        if ($helper->isCleanupProbability()) {
            $this->_getResource()->deleteOldEntries($helper->getCleanupExpirationPeriod());
        }
        return $this;
    }
}
