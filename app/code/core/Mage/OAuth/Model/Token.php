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
 * @method Mage_OAuth_Model_Resource_Token _getResource()
 * @method Mage_OAuth_Model_Resource_Token getResource()
 * @method Mage_OAuth_Model_Resource_Token_Collection getCollection()
 * @method Mage_OAuth_Model_Resource_Token_Collection getResourceCollection()
 * @method string getConsumerId()
 * @method Mage_OAuth_Model_Token setConsumerId() setConsumerId(string $consumerId)
 * @method string getCustomerId()
 * @method string getTmpToken()
 * @method Mage_OAuth_Model_Token setTmpToken() setTmpToken(string $token)
 * @method string getTmpTokenSecret()
 * @method Mage_OAuth_Model_Token setTmpTokenSecret() setTmpTokenSecret(string $tokenSecret)
 * @method string getTmpVerifier()
 * @method string getTmpCallbackUrl()
 * @method Mage_OAuth_Model_Token setTmpCallbackUrl() setTmpCallbackUrl(string $callbackUrl)
 * @method string getTmpCreatedAt()
 * @method string getToken()
 * @method Mage_OAuth_Model_Token setToken() setToken(string $token)
 * @method string getTokenSecret()
 * @method Mage_OAuth_Model_Token setTokenSecret() setTokenSecret(string $tokenSecret)
 */
class Mage_OAuth_Model_Token extends Mage_Core_Model_Abstract
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('oauth/token');
    }
}
