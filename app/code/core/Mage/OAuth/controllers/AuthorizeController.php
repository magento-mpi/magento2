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
 * oAuth authorize controller
 *
 * @category    Mage
 * @package     Mage_OAuth
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_OAuth_AuthorizeController extends Mage_Core_Controller_Front_Action
{
    /**
     * Index action.
     *
     * @return void
     */
    public function indexAction()
    {
        $oauthToken = $this->getRequest()->getQuery('oauth_token', null);

        if (null === $oauthToken) {
            die('Invalid token');
        }
        /** @var $token Mage_OAuth_Model_Token */
        $token = Mage::getModel('oauth/token');

        if (!$token->load($oauthToken, 'tmp_token')->getId()) {
            die('Invalid token');
        }
        /** @var $consumer Mage_OAuth_Model_Consumer */
        $consumer = Mage::getModel('oauth/consumer');

        if (!$consumer->load($token->getConsumerId())->getId()) {
            die('Invalid token');
        }
        /** @var $helper Mage_OAuth_Helper_Data */
        $helper = Mage::helper('oauth');

        $token->setTmpVerifier($helper->generateToken(32));
        $token->save();

        $callbackUrl = $token->getTmpCallbackUrl() . '?oauth_token=' . $token->getTmpToken()
                       . '&amp;oauth_verifier=' . $token->getTmpVerifier();

        // Authentication form HTML
        echo 'Here will be user auth form<br/><a href="' . $callbackUrl
             . '">Yes, I grant rights for ' . $consumer->getName() . '</a><br>' . htmlspecialchars($callbackUrl);
    }
}
