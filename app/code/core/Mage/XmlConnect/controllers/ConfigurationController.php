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

class Mage_XmlConnect_ConfigurationController extends Mage_XmlConnect_Controller_Action
{
    /**
     * Initialize application
     * @param string $paramName
     * @return Mage_XmlConnect_Model_Application
     */
    protected function _initApp()
    {
        $app = Mage::registry('current_app');
        if ($app && $app->getId()) {
            $app->loadConfiguration();
        }
        else {
            Mage::throwException($this->__('Aplication with id "%s" no longer exists.', $id));
        }
        return $app;
    }

    /**
     * Default action
     */
    public function indexAction()
    {
        try {
            $app = $this->_initApp();
            if( $this->getRequest()->getParam('updated_at') ) {
                $updated_at = strtotime($app->getUpdatedAt());
                $loaded_at = (int) $this->getRequest()->getParam('updated_at');
                if( $loaded_at >= $updated_at ) {
                    $message = new Varien_Simplexml_Element('<message></message>');
                    $message->addChild('status', self::MESSAGE_STATUS_SUCCESS);
                    $message->addChild('no_changes', '1');
                    $this->getResponse()->setBody($message->asNiceXml());
                    return;
                }
            }
            $this->loadLayout(false);
            $this->renderLayout();
        } catch (Mage_Core_Exception $e) {
            $this->_message($e->getMessage(), self::MESSAGE_STATUS_ERROR);
        } catch (Exception $e) {
            $this->_message($this->__('Cannot show configuration.'), self::MESSAGE_STATUS_ERROR);
        }

    }
}