<?php
/**
 * Oauth Nonce Model
 *
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 * @author Magento Core Team <core@magentocommerce.com>
 * @method string getNonce()
 * @method Magento_Oauth_Model_Nonce setNonce() setNonce(string $nonce)
 * @method int getConsumerId()
 * @method Magento_Oauth_Model_Nonce setConsumerId() setConsumerId(int $consumerId)
 * @method string getTimestamp()
 * @method Magento_Oauth_Model_Nonce setTimestamp() setTimestamp(string $timestamp)
 * @method Magento_Oauth_Model_Resource_Nonce getResource()
 * @method Magento_Oauth_Model_Resource_Nonce _getResource()
 */
class Magento_Oauth_Model_Nonce extends Magento_Core_Model_Abstract
{
    /**
     * Oauth data
     *
     * @var Magento_Oauth_Helper_Service
     */
    protected $_oauthData = null;

    /**
     * @param Magento_Oauth_Helper_Service $oauthData
     * @param Magento_Core_Model_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Core_Model_Resource_Abstract $resource
     * @param Magento_Data_Collection_Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        Magento_Oauth_Helper_Service $oauthData,
        Magento_Core_Model_Context $context,
        Magento_Core_Model_Registry $registry,
        Magento_Core_Model_Resource_Abstract $resource = null,
        Magento_Data_Collection_Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_oauthData = $oauthData;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

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

        if ($this->_oauthData->isCleanupProbability()) {
            $this->_getResource()->deleteOldEntries($this->_oauthData->getCleanupExpirationPeriod());
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
