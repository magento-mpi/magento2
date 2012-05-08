<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Index backend controller
 *
 * @category    Mage
 * @package     Mage_Backend
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Backend_Adminhtml_IndexController extends Mage_Backend_Controller_ActionAbstract
{
    /**
     * Admin area entry point
     * Always redirects to the startup page url
     */
    public function indexAction()
    {
        $session = Mage::getSingleton('Mage_Admin_Model_Session');
        if ($session->isLoggedIn()) {
            $this->_redirect($session->getUser()->getStartupPageUrl());
        } else {
            $this->_redirect('adminhtml/auth/index');
        }
    }
}
