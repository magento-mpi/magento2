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
        $session  = Mage::getSingleton('admin/session');
        $request = Mage::registry('controller')->getRequest();
        $user = $session->getUser();

        if (!$user) {
            if ($request->getPost('login')) {
                extract($request->getPost('login'));
                if (!empty($username) && !empty($password)) {
                    $user = Mage::getModel('admin/user')->login($username, $password);
                    if ($user->getId()) {
                        $session->setUser($user);
                        header('Location: '.$request->getRequestUri());
                        exit;
                    }
                }
            } 
            if (!$request->getParam('forwarded')) {
                $request->setParam('forwarded', true)
                    ->setControllerName('index')                
                    ->setActionName('login')
                    ->setDispatched(false);
                return false;
            }
        } else {
            $user->reload();
            if (!$session->getAcl() || $user->getReloadAclFlag()) {
                $session->setAcl(Mage::getResourceModel('admin/acl')->loadAcl());
            }
            if ($user->getReloadAclFlag()) {
                $user->setReloadAclFlag(0)->save();
            }
        }
    }
}
