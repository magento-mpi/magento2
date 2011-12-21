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
 * Validate consumer callback URL
 *
 * @category   Mage
 * @package    Mage_OAuth
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_OAuth_Model_Consumer_Validator_CallbackUrl extends Zend_Validate_Abstract
{
    /**#@+
     * Error keys
     */
    const NO_OAUTH_PREFIX = 'noOAuthPrefix';
    const INVALID_URL     = 'invalidUrl';
    /**#@-*/

    /**
     * Init validation failure message template definitions
     */
    protected function _initMessageTemplates()
    {
        /** @var $helper Mage_OAuth_Helper_Data */
        $helper = Mage::helper('oauth');
        //init messages
        $this->_messageTemplates = array(
            self::NO_OAUTH_PREFIX =>
                $helper->__("Parameters in URL '%value%' must not contain 'oauth_' prefix. See RFC-5849."),
            self::INVALID_URL     => $helper->__("Invalid URL '%value%'."));
    }

    /**
     * Validate value
     *
     * @param string $value
     * @return bool
     */
    public function isValid($value)
    {
        $this->_setValue($value);

        //check valid URL
        /** @var $url Mage_Core_Model_Url_Validator */
        $url = Mage::getModel('core/url_validator');
        if (!$url->isValid($value)) {
            $this->_error(self::INVALID_URL);
            return false;
        }

        //check prefix "oauth_" in parameters
        if (preg_match('/[&?]oauth_/i', $value)) {
            $this->_error(self::NO_OAUTH_PREFIX);
            return false;
        }

        return true;
    }
}
