<?php

/**
 * Auth session model
 * 
 * @package     Mage
 * @subpackage  Admin
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Moshe Gurvich <moshe@varien.com>
 */
class Mage_Admin_Model_Session extends Mage_Core_Model_Session_Abstract 
{
    public function __construct()
    {
        $this->init('admin');
    }
    
    /**
     * Check current user permission on resource and privilege
     * 
     * Mage::getSingleton('admin/session')->isAllowed('admin/catalog')
     * Mage::getSingleton('admin/session')->isAllowed('catalog')
     *
     * @param   string $resource
     * @param   string $privilege
     * @return  bool
     */
    public function isAllowed($resource, $privilege=null)
    {
        if ($this->getUser() && $this->getAcl()) {
            if (!preg_match('/^admin/', $resource)) {
            	$resource = 'admin/'.$resource;
            }
        	return $this->getAcl()->isAllowed($this->getUser()->getAclRole(), $resource, $privilege);
        	//return $this->getAcl()->isAllowed('G2', $resource, $privilege);
        }
        return false;
    }
}