<?php
/**
 * {license}
 *
 * @category    Mage
 * @package     Mage_OAuth
 */


/**
 * oAuth nonce model
 *
 * @category    Mage
 * @package     Mage_OAuth
 * @author      Magento Core Team <core@magentocommerce.com>
 * @method string getNonce()
 * @method Mage_OAuth_Model_Nonce setNonce() setNonce(string $nonce)
 * @method string getTimestamp()
 * @method Mage_OAuth_Model_Nonce setTimestamp() setTimestamp(string $timestamp)
 * @method Mage_OAuth_Model_Resource_Nonce getResource()
 * @method Mage_OAuth_Model_Resource_Nonce _getResource()
 */
class Mage_OAuth_Model_Nonce extends Mage_Core_Model_Abstract
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Mage_OAuth_Model_Resource_Nonce');
    }

    /**
     * "After save" actions
     *
     * @return Mage_OAuth_Model_Nonce
     */
    protected function _afterSave()
    {
        parent::_afterSave();

        //Cleanup old entries
        /** @var $helper Mage_OAuth_Helper_Data */
        $helper = Mage::helper('Mage_OAuth_Helper_Data');
        if ($helper->isCleanupProbability()) {
            $this->_getResource()->deleteOldEntries($helper->getCleanupExpirationPeriod());
        }
        return $this;
    }
}
