<?php
/**
 * Oauth Nonce Model
 *
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */


/**
 * oAuth nonce model
 *
 * @author Magento Core Team <core@magentocommerce.com>
 * @method string getNonce()
 * @method Mage_Oauth_Model_Nonce setNonce() setNonce(string $nonce)
 * @method int getConsumerId()
 * @method Mage_Oauth_Model_Nonce setConsumerId() setConsumerId(int $consumerId)
 * @method string getTimestamp()
 * @method Mage_Oauth_Model_Nonce setTimestamp() setTimestamp(string $timestamp)
 * @method Mage_Oauth_Model_Resource_Nonce getResource()
 * @method Mage_Oauth_Model_Resource_Nonce _getResource()
 */
class Mage_Oauth_Model_Nonce extends Mage_Core_Model_Abstract
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

        /** @var $helper Mage_Oauth_Helper_Data */
        $helper = Mage::helper('Mage_Oauth_Helper_Data');
        if ($helper->isCleanupProbability()) {
            $this->_getResource()->deleteOldEntries($helper->getCleanupExpirationPeriod());
        }
        return $this;
    }

    /**
     * Load given a composite key consisting of a nonce string and a consumer id
     *
     * @param string $nonce - The nonce string
     * @param int $consumerId - The consumer id
     * @return $this
     */
    public function loadByCompositeKey($nonce, $consumerId)
    {
        $this->setData($this->getResource()->selectByCompositeKey($nonce, $consumerId));
        return $this;
    }
}
