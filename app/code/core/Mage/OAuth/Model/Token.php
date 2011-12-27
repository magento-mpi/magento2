<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_OAuth
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * oAuth token model
 *
 * @category    Mage
 * @package     Mage_OAuth
 * @author      Magento Core Team <core@magentocommerce.com>
 * @method Mage_OAuth_Model_Resource_Token_Collection getCollection()
 * @method Mage_OAuth_Model_Resource_Token_Collection getResourceCollection()
 * @method Mage_OAuth_Model_Resource_Token getResource()
 * @method Mage_OAuth_Model_Resource_Token _getResource()
 * @method int getConsumerId()
 * @method Mage_OAuth_Model_Token setConsumerId() setConsumerId(int $consumerId)
 * @method int getAdminId()
 * @method Mage_OAuth_Model_Token setAdminId() setAdminId(int $adminId)
 * @method string getTmpToken()
 * @method Mage_OAuth_Model_Token setTmpToken() setTmpToken(string $tmpToken)
 * @method string getTmpTokenSecret()
 * @method Mage_OAuth_Model_Token setTmpTokenSecret() setTmpTokenSecret(string $tmpTokenSecret)
 * @method string getVerifier()
 * @method Mage_OAuth_Model_Token setVerifier() setVerifier(string $verifier)
 * @method string getCallbackUrl()
 * @method Mage_OAuth_Model_Token setCallbackUrl() setCallbackUrl(string $callbackUrl)
 * @method string getTmpCreatedAt()
 * @method Mage_OAuth_Model_Token setTmpCreatedAt() setTmpCreatedAt(string $tmpCreatedAt)
 * @method string getToken()
 * @method Mage_OAuth_Model_Token setToken() setToken(string $token)
 * @method string getSecret()
 * @method Mage_OAuth_Model_Token setSecret() setSecret(string $tokenSecret)
 * @method int getIsRevoked()
 * @method Mage_OAuth_Model_Token setIsRevoked() setIsRevoked(int $isRevoked)
 * @method int getAuthorized()
 * @method Mage_OAuth_Model_Token setAuthorized() setAuthorized(int $authorized)
 */
class Mage_OAuth_Model_Token extends Mage_Core_Model_Abstract
{
    /**#@+
     * Token types
     */
    const TYPE_REQUEST = 'request';
    const TYPE_ACCESS  = 'access';
    /**#@- */

    /**#@+
     * Lengths of token fields
     */
    const LENGTH_TOKEN    = 32;
    const LENGTH_SECRET   = 32;
    const LENGTH_VERIFIER = 32;
    /**#@- */

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('oauth/token');
    }

    /**
     * Authorize token
     *
     * @return Mage_OAuth_Model_Token
     */
    public function authorize()
    {
        if ($this->getAuthorized()) {
            Mage::throwException('Token is already authorized');
        }
        /** @var $helper Mage_OAuth_Helper_Data */
        $helper = Mage::helper('oauth');

        $this->setVerifier($helper->generateVerifier());
        $this->setAuthorized(1);
        $this->save();

        return $this;
    }

    /**
     * Load token by temporary token
     *
     * @param string $tmpToken
     * @return Mage_OAuth_Model_Token
     */
    public function loadByTmpToken($tmpToken)
    {
        $this->_getResource()->loadByTmpToken($this, $tmpToken);
        return $this;
    }

    /**
     * Get string representation of token
     *
     * @param string $format
     * @return string
     */
    public function toString($format = '')
    {
        return http_build_query(array('oauth_token' => $this->getToken(), 'oauth_token_secret' => $this->getSecret()));
    }
}
