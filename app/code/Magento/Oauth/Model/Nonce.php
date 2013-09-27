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
     * Oauth data
     *
     * @var \Magento\Oauth\Helper\Service
     */
    protected $_oauthData = null;

    /**
     * @param \Magento\Oauth\Helper\Service $oauthData
     * @param \Magento\Core\Model\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Core\Model\Resource\AbstractResource $resource
     * @param \Magento\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Oauth\Helper\Service $oauthData,
        \Magento\Core\Model\Context $context,
        \Magento\Core\Model\Registry $registry,
        \Magento\Core\Model\Resource\AbstractResource $resource = null,
        \Magento\Data\Collection\Db $resourceCollection = null,
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
