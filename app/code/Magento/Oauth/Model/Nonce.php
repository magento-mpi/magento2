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
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('\Magento\Oauth\Model\Resource\Nonce');
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
        /** @var $helper \Magento\Oauth\Helper\Data */
        $helper = \Mage::helper('Magento\Oauth\Helper\Data');
        if ($helper->isCleanupProbability()) {
            $this->_getResource()->deleteOldEntries($helper->getCleanupExpirationPeriod());
        }
        return $this;
    }
}
