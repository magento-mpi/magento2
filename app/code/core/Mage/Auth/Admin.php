<?php

/**
 * Utility class for Auth Admin module
 * 
 * @package     Mage
 * @subpackage  Auth
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Moshe Gurvich <moshe@varien.com>
 */
class Mage_Auth_Admin
{
    /**
     * Run this method every time before admin controller action dispatch
     * 
     * Checks for user authentication and loads acl authorizations
     *
     */
    static public function action_preDispatch()
    {
        $auth = Mage::getSingleton('auth', 'session');

#$auth->acl = null;
        $request = Mage::registry('controller')->getRequest();

        if (!$auth->getUser() && $request->getPost('login')) {
            extract($request->getPost('login'));
            if (!empty($username) && !empty($password)) {
                $auth->setUser(Mage::getModel('auth_resource', 'auth')->authenticate($username, $password));
                header('Location: '.$request->getRequestUri());
                die();
            }
        }
        
        if (!$auth->getUser()) {
            echo Mage::getModel('core', 'layout')->createBlock('tpl', 'root')
                ->setTemplate('auth/login.phtml')
                ->assign('username', '')
                ->toHtml();
            exit;
        }
       
        if (!$auth->getAcl()) {
            $auth->setAcl(Mage::getModel('auth_resource', 'acl')->loadUserAcl($auth->getUser()->user_id));
        }
        
        Mage::register('acl', $auth->getAcl());
    }


/*
Example of acl query: Mage::getSingleton('auth', 'session')->getAcl()->isAllowed('U2', 'system/websites')
                  or: Mage::registry('acl')->isAllowed('U2', 'system/websites')
*/

}