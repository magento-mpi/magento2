<?php
/**
 * Admin observer model
 *
 * @package     Mage
 * @subpackage  Admin
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Admin_Model_Observer
{
    public function actionPreDispatchAdmin()
    {
        Mage::log('Admin observer: preDispatch admin action');
        $session  = Mage::getSingleton('admin', 'session');
        $request= Mage::registry('controller')->getRequest();

        //$session->unsetAll();
        if (!$session->getUser() && $request->getPost('login')) {
            extract($request->getPost('login'));
            if (!empty($username) && !empty($password)) {
                $session->setUser(Mage::getModel('admin_resource', 'user')->authenticate($username, $password));
                header('Location: '.$request->getRequestUri());
                exit;
            }
        }
        else {
            // TODO: check reload user ACL table
        }
        
        if (!$session->getUser() && !$request->getParam('forwarded')) {
            $request->setParam('forwarded', true)
                ->setControllerName('index')                
                ->setActionName('login')
                ->setDispatched(false);
            return false;
        }
       
        if ($session->getUser() && !$session->getAcl()) {
            $session->setAcl(Mage::getModel('admin_resource', 'acl')->loadUserAcl($session->getUser()->getId()));
        }
        //var_dump($session->isAllowed('admin'));die();
        
        Mage::getSingleton('core', 'translate')->loadTranslationFile('admin/base.csv');        
    }
}
