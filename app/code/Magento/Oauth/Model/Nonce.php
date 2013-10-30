<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Application model
 * @author Magento Core Team <core@magentocommerce.com>
 * @method string getNonce()
 * @method \Magento\Oauth\Model\Nonce setNonce() setNonce(string $nonce)
 * @method int getConsumerId()
 * @method \Magento\Oauth\Model\Nonce setConsumerId() setConsumerId(int $consumerId)
 * @method string getTimestamp()
 * @method \Magento\Oauth\Model\Nonce setTimestamp() setTimestamp(string $timestamp)
 * @method \Magento\Oauth\Model\Resource\Nonce getResource()
 * @method \Magento\Oauth\Model\Resource\Nonce _getResource()
 */
namespace Magento\Oauth\Model;

class Nonce extends \Magento\Core\Model\AbstractModel
{
    /**
     * Nonce length
     */
    const NONCE_LENGTH = 32;

    /**
     * Oauth data
     *
     * @var \Magento\Oauth\Helper\Data
     */
    protected $_oauthData;

    /**
     * @param \Magento\Oauth\Helper\Data $oauthData
     * @param \Magento\Core\Model\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Core\Model\Resource\AbstractResource $resource
     * @param \Magento\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Oauth\Helper\Data $oauthData,
        \Magento\Core\Model\Context $context,
        \Magento\Core\Model\Registry $registry,
        \Magento\Core\Model\Resource\AbstractResource $resource = null,
        \Magento\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->_oauthData = $oauthData;
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magento\Oauth\Model\Resource\Nonce');
    }

    /**
     * "After save" actions
     *
     * @return \Magento\Oauth\Model\Nonce
     */
    protected function _afterSave()
    {
        parent::_afterSave();

        if ($this->_oauthData->isCleanupProbability()) {
            $this->getResource()->deleteOldEntries($this->_oauthData->getCleanupExpirationPeriod());
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
        $data = $this->getResource()->selectByCompositeKey($nonce, $consumerId);
        $this->setData($data ? $data : array());
        return $this;
    }
}
