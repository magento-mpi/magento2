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
 * @method \Magento\Oauth\Model\Nonce setNonce() setNonce(string $nonce)
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
     * @var \Magento\Oauth\Helper\Data
     */
    protected $_oauthData = null;

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

        //Cleanup old entries
        if ($this->_oauthData->isCleanupProbability()) {
            $this->_getResource()->deleteOldEntries($this->_oauthData->getCleanupExpirationPeriod());
        }
        return $this;
    }
}
