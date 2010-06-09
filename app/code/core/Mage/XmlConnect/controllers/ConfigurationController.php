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
 * @package     Mage_XmlConnect
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * XmlConnect index controller
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_XmlConnect_ConfigurationController extends Mage_Core_Controller_Front_Action
{
    /**
     * Declare content type header
     */
    public function preDispatch()
    {
        parent::preDispatch();
        $this->getResponse()->setHeader('Content-type', 'text/xml; charset=UTF-8');
    }

    /**
     * Initialize application
     *
     * @return Mage_XmlConnect_Model_Application
     */
    protected function _initApp()
    {
        $cookieName = Mage_XmlConnect_Model_Application::APP_CODE_COOKIE_NAME;
        $code = $this->getRequest()->getParam($cookieName);
        $app = Mage::getModel('xmlconnect/application');
        if ($app) {
            $app->loadByCode($code);
            if (!$app->getId()) {
                Mage::throwException($this->__('Aplication with specified code no longer exists.'));
            }
            $app->loadConfiguration();
        }
        else {
            Mage::throwException($this->__('Aplication code required.'));
        }
        Mage::register('current_app', $app);
        return $app;
    }

    /**
     * Default action
     */
    public function indexAction()
    {
        try {
            $app = $this->_initApp();

            $cookieName = Mage_XmlConnect_Model_Application::APP_CODE_COOKIE_NAME;
            if (!isset($_COOKIE[$cookieName])) {
                /**
                 * @todo add management of cookie expire to application admin panel
                 */
                $cookieExpireOffset = 3600 * 24 * 30;
                setcookie($cookieName, $app->getCode(), time() + $cookieExpireOffset, '/', null, null, true);
            }

            if($this->getRequest()->getParam('updated_at')) {
                $updated_at = strtotime($app->getUpdatedAt());
                $loaded_at = (int) $this->getRequest()->getParam('updated_at');
                if($loaded_at >= $updated_at) {
                    $message = new Varien_Simplexml_Element('<message></message>');
                    $message->addChild('status', Mage_XmlConnect_Controller_Action::MESSAGE_STATUS_SUCCESS);
                    $message->addChild('no_changes', '1');
                    $this->getResponse()->setBody($message->asNiceXml());
                    return;
                }
            }
            $this->loadLayout(false);
            $this->renderLayout();
        }
        catch (Mage_Core_Exception $e) {
            $message = new Varien_Simplexml_Element('<message></message>');
            $message->addChild('status', Mage_XmlConnect_Controller_Action::MESSAGE_STATUS_ERROR);
            $message->addChild('text', $e->getMessage());
            $this->getResponse()->setBody($message->asNiceXml());
        }
        catch (Exception $e) {
        var_dump($e); die();
            $message = new Varien_Simplexml_Element('<message></message>');
            $message->addChild('status', Mage_XmlConnect_Controller_Action::MESSAGE_STATUS_ERROR);
            $message->addChild('text', $this->__('Cannot show configuration.'));
            $this->getResponse()->setBody($message->asNiceXml());
        }

    }
}
