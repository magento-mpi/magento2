<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */
namespace Magento\Integration\Model\Oauth;

/**
 * Nonce model
 * @author Magento Core Team <core@magentocommerce.com>
 * @method string getNonce()
 * @method \Magento\Integration\Model\Oauth\Nonce setNonce() setNonce(string $nonce)
 * @method int getConsumerId()
 * @method \Magento\Integration\Model\Oauth\Nonce setConsumerId() setConsumerId(int $consumerId)
 * @method string getTimestamp()
 * @method \Magento\Integration\Model\Oauth\Nonce setTimestamp() setTimestamp(string $timestamp)
 * @method \Magento\Integration\Model\Resource\Oauth\Nonce getResource()
 * @method \Magento\Integration\Model\Resource\Oauth\Nonce _getResource()
 */
class Nonce extends \Magento\Model\AbstractModel
{
    /**
     * Oauth data
     *
     * @var \Magento\Integration\Helper\Oauth\Data
     */
    protected $_oauthData;

    /**
     * @param \Magento\Model\Context $context
     * @param \Magento\Registry $registry
     * @param \Magento\Integration\Helper\Oauth\Data $oauthData
     * @param \Magento\Model\Resource\AbstractResource $resource
     * @param \Magento\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Model\Context $context,
        \Magento\Registry $registry,
        \Magento\Integration\Helper\Oauth\Data $oauthData,
        \Magento\Model\Resource\AbstractResource $resource = null,
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
        $this->_init('Magento\Integration\Model\Resource\Oauth\Nonce');
    }

    /**
     * The "After save" actions
     *
     * @return $this
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
        $this->setData($data);
        return $this;
    }
}
