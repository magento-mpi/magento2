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
 * @package     Mage_Api2
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * oAuth Authenticatin adapter
 *
 * @category   Mage
 * @package    Mage_Api2
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Api2_Model_Auth_Adapter_Oauth extends Mage_Api2_Model_Auth_Adapter_Abstract
{
    /**
     * Process request and figure out an API user role
     *
     * @param Mage_Api2_Model_Request $request
     * @return string|boolean Return boolean FALSE if can not determine user role
     */
    public function getUserRole(Mage_Api2_Model_Request $request)
    {
        $headerValue = $request->getHeader('Authorization');

        if (!$headerValue || 'OAuth' !== substr($headerValue, 0, 5)) {
            return false;
        }
        /** @var $oauthServer Mage_OAuth_Model_Server */
        $oauthServer = Mage::getModel('oauth/server', $request);
        $requestUrl  = $request->getScheme() . '://' . $request->getHttpHost() . $request->getRequestUri();

        try {
            return $oauthServer->checkAccessRequest($requestUrl)->getUserType();
        } catch (Exception $e) {
            return false;
        }
    }
}
