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
        /** @var $server Mage_OAuth_Model_Server */
        $server = Mage::getModel('oauth/server');

        $server->checkAuthorizeRequest();

        $token = $server->authorizeToken();

        $callbackUrl = $token->getCallbackUrl() . '?oauth_token=' . $token->getToken()
                       . '&amp;oauth_verifier=' . $token->getVerifier();

        /** @var $consumer Mage_OAuth_Model_Consumer */
        $consumer = Mage::getModel('oauth/consumer');

        $consumer->load($token->getConsumerId());

        if (!$consumer->getId()) {
            Mage::throwException('Invalid consumer');
        }

        // Authentication form HTML
//        echo 'Here will be user auth form<br/><a href="' . $callbackUrl . '">Yes, I grant rights for '
//            . $consumer->getName() . '</a><br>' . htmlspecialchars($callbackUrl);

        $this->loadLayout();
        $this->renderLayout();
    }
}
