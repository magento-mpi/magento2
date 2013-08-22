<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Oauth
 * @copyright  {copyright}
 * @license    {license_link}
 */


/**
 * oAuth nonce model
 *
 * @category    Magento
 * @package     Magento_Oauth
 * @author      Magento Core Team <core@magentocommerce.com>
 * @method string getNonce()
 * @method Magento_Oauth_Model_Nonce setNonce() setNonce(string $nonce)
 * @method string getTimestamp()
 * @method Magento_Oauth_Model_Nonce setTimestamp() setTimestamp(string $timestamp)
 * @method Magento_Oauth_Model_Resource_Nonce getResource()
 * @method Magento_Oauth_Model_Resource_Nonce _getResource()
 */
class Magento_Oauth_Model_Nonce extends Magento_Core_Model_Abstract
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magento_Oauth_Model_Resource_Nonce');
    }

    /**
     * "After save" actions
     *
     * @return Magento_Oauth_Model_Nonce
     */
    protected function _afterSave()
    {
        parent::_afterSave();

        //Cleanup old entries
        /** @var $helper Magento_Oauth_Helper_Data */
        $helper = Mage::helper('Magento_Oauth_Helper_Data');
        if ($helper->isCleanupProbability()) {
            $this->_getResource()->deleteOldEntries($helper->getCleanupExpirationPeriod());
        }
        return $this;
    }
}
