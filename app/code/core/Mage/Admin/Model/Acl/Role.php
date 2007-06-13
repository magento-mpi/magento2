<?php

/**
 * User acl role
 * 
 * @package     Mage
 * @subpackage  Admin
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Moshe Gurvich <moshe@varien.com>
 */
class Mage_Admin_Model_Acl_Role extends Zend_Acl_Role 
{
    protected $_data = array();
    
    public function addData(array $data)
    {
        foreach ($data as $k=>$v) {
            $_data[$k] = $v;
        }
        return $this;
    }
    
    public function getData($key=null)
    {
        if (empty($key)) {
            return $this->_data;
        } elseif (isset($this->_data[$key])) {
            return $this->_data[$key];
        }
        return false;
    }
}