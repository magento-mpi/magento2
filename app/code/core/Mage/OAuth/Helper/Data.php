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
 * OAuth Helper
 *
 * @category    Mage
 * @package     Mage_OAuth
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_OAuth_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**#@+
     * Endpoint types with appropriate routes
     */
    const ENDPOINT_AUTHORIZE_CUSTOMER = 'oauth/authorize';
    const ENDPOINT_AUTHORIZE_ADMIN    = 'adminhtml/oAuth_authorize';
    const ENDPOINT_INITIATE           = 'oauth/initiate';
    const ENDPOINT_TOKEN              = 'oauth/token';
    /**#@-*/

    /**
     * Generate random string for token or secret or verifier
     *
     * @param int $length String length
     * @return string
     */
    protected function _generateRandomString($length)
    {
        /** @var $helper Mage_Core_Helper_Data */
        $helper = Mage::helper('core');

        return $helper->getRandomString(
            $length, Mage_Core_Helper_Data::CHARS_DIGITS . Mage_Core_Helper_Data::CHARS_LOWERS
        );
    }

    /**
     * Generate random string for token
     *
     * @return string
     */
    public function generateToken()
    {
        return $this->_generateRandomString(Mage_OAuth_Model_Token::LENGTH_TOKEN);
    }

    /**
     * Generate random string for token secret
     *
     * @return string
     */
    public function generateTokenSecret()
    {
        return $this->_generateRandomString(Mage_OAuth_Model_Token::LENGTH_SECRET);
    }

    /**
     * Generate random string for verifier
     *
     * @return string
     */
    public function generateVerifier()
    {
        return $this->_generateRandomString(Mage_OAuth_Model_Token::LENGTH_VERIFIER);
    }

    /**
     * Generate random string for consumer key
     *
     * @return string
     */
    public function generateConsumerKey()
    {
        return $this->_generateRandomString(Mage_OAuth_Model_Consumer::KEY_LENGTH);
    }

    /**
     * Generate random string for consumer secret
     *
     * @return string
     */
    public function generateConsumerSecret()
    {
        return $this->_generateRandomString(Mage_OAuth_Model_Consumer::SECRET_LENGTH);
    }

    /**
     * Retrieve URL of specified endpoint.
     *
     * @param string $type Endpoint type (one of ENDPOINT_ constants)
     * @return string
     */
    public function getProtocolEndpointUrl($type)
    {
        if (self::ENDPOINT_INITIATE != $type
            && self::ENDPOINT_AUTHORIZE_CUSTOMER != $type
            && self::ENDPOINT_AUTHORIZE_ADMIN != $type
            && self::ENDPOINT_TOKEN != $type
        ) {
            Mage::throwException('Invalid endpoint type passed');
        }
        return rtrim(Mage::getUrl($type), '/');
    }
}
