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
 * oAuth initiate controller
 *
 * @category    Mage
 * @package     Mage_OAuth
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_OAuth_InitiateController extends Mage_Core_Controller_Front_Action
{
    /**
     * Test action to generate initiative request to oAuth server
     */
    public function clientInitAction()
    {
        /** @var $helper Mage_OAuth_Helper_Data */
        $helper = Mage::app()->getHelper('oauth');

        $config = array(
            'callbackUrl'     => $helper->getProtocolEndpointUrl(Mage_OAuth_Helper_Data::ENDPOINT_AUTHORIZE),
            'requestTokenUrl' => $helper->getProtocolEndpointUrl(Mage_OAuth_Helper_Data::ENDPOINT_INITIATE),
            'consumerKey'     => 'dpf43f3p2l4k3l03',
            'consumerSecret'  => 'kd94hf93k423kf44',
            'signatureMethod' => 'HMAC-SHA1',
            'version'         => '1.0'
        );
        $consumer = new Zend_Oauth_Consumer($config);

        echo $consumer->getRequestToken();
    }

    /**
     * Index action.  Receive initiate request and response OAuth token
     */
    public function indexAction()
    {
        /** @var $server Mage_OAuth_Model_Server */
        $server = Mage::getModel('oauth/server');

        $server->setRequest($this->getRequest());
        $server->setResponse($this->getResponse());

        $server->initiateToken();
    }
}
