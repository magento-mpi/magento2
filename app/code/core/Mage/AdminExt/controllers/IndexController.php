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
 * @category   Mage
 * @package    Mage_AdminExt
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Admin index controller
 *
 * @category   Mage
 * @package    Mage_AdminExt
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Admin_IndexController extends Mage_Core_Controller_Front_Action
{
    function indexAction()
    {
        $this->loadLayout('admin'); 
        $this->renderLayout();
    }
    
    function applyDbUpdatesAction()
    {
        Mage_Core_Model_Resource_Setup::applyAllUpdates();
        echo "Successfully updated.";
    }
    
    public function loginAction()
    {
        $block = Mage::getModel('core/layout')->createBlock('core/template', 'root')
                ->setTemplate('admin/login.phtml')
                ->assign('username', '');
            
        $this->getResponse()->setBody($block->toHtml());
    }
    
    public function logoutAction()
    {
        $auth = Mage::getSingleton('admin/session')->unsetAll();
        $this->_redirect('admin');
    }
}