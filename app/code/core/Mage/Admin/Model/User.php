<?php
/**
 * ACL user model
 *
 * @package     Mage
 * @subpackage  Admin
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Admin_Model_User extends Varien_Object
{
    public function __construct() 
    {
        parent::__construct();
    }
    
    /**
     * Get user id
     *
     * @return int || null
     */
    public function getId()
    {
        return $this->getUserId();
    }
    
    /**
     * Get user ACL role
     *
     * @return string
     * @todo   dynamic defination user role
     */
    public function getAclRole()
    {
        return 'G1';
    }
    
    /**
     * Get resource model
     *
     * @return mixed
     */
    public function getResource()
    {
        return Mage::getSingleton('admin_resource', 'user');
    }
    
    /**
     * Authenticate user
     *
     * @param   string $login
     * @param   string $password
     * @return  Mage_Admin_Model_User
     */
    public function authenticate($login, $password)
    {
        if ($this->getResource()->authenticate($this, $login, $password)) {
            return $this;
        }
        return false;
    }
    
    /**
     * Load user data by user id
     *
     * @param   int $userId
     * @return  Mage_Admin_Model_User
     */
    public function load($userId)
    {
        $this->setData($this->getResource()->load($userId));
        return $this;
    }
    
    public function loadByUsername($username)
    {
        $this->setData($this->getResource()->loadByUsername($username));
        return $this;
    }
    
    /**
     * Save user data
     *
     * @return Mage_Admin_Model_User
     */
    public function save()
    {
        $this->getResource()->save($this);
        return $this;
    }
    
    /**
     * Delete user
     *
     * @return Mage_Admin_Model_User
     */
    public function delete()
    {
        $this->getResource()->delete($this);
        return $this;
    }
    
    /**
     * Run this method every time before admin controller action dispatch
     * 
     * Checks for user authentication and loads acl authorizations
     *
     */
    public function preDispatchModuleAction()
    {
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
    }
    
    public function loadLanguageFile()
    {
        Mage::getSingleton('core', 'translate')->loadTranslationFile('admin/base.csv');
    }
}
