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
 * @category   Varien
 * @package    Varien_Convert
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Convert component abstract
 *
 * @category   Varien
 * @package    Varien_Convert
 * @author     Moshe Gurvich <moshe@varien.com>
 */
abstract class Varien_Convert_Component_Abstract implements Varien_Convert_Component_Interface
{
    protected $_params;
    protected $_vars;
    protected $_profile;
    protected $_data;
    protected $_method;

    public function getParam($key, $default=null)
    {
        if (!isset($this->_params[$key])) {
            return $default;
        }
        return $this->_params[$key];
    }
    
    public function getParams()
    {
        return $this->_params;
    }
    
    public function setParam($key, $value=null)
    {
        if (is_array($key) && is_null($value)) {
            $this->_param = $key;
        } else {
            $this->_param[$key] = $value;
        }
        return $this;
    }
    
    public function getVar($key, $default=null)
    {
        if (!isset($this->_vars[$key])) {
            return $default;
        }
        return $this->_vars[$key];
    }
    
    public function getVars()
    {
        return $this->_vars;
    }
    
    public function setVar($key, $value=null)
    {
        if (is_array($key) && is_null($value)) {
            $this->_vars = $key;
        } else {
            $this->_vars[$key] = $value;
        }
        return $this;
    }

    public function getProfile()
    {
        return $this->_profile;
    }
    
    public function setProfile(Varien_Convert_Profile_Abstract $profile)
    {
        $this->_profile = $profile;
        return $this;
    }
        
    public function getData()
    {
        if (is_null($this->_data) && $this->getProfile()) {
            $this->_data = $this->getProfile()->getDefaultContainer()->getData();
        }
        return $this->_data;
    }
    
    public function setData($data)
    {
        if ($this->getProfile()) {
            $this->getProfile()->getDefaultContainer()->setData($data);
        }
        $this->_data = $data;
        return $this;   
    }
        
    public function getMethod()
    {
        return $this->_method;
    }
    
    public function setMethod($method)
    {
        $this->_method = $method;
        return $this;   
    }
        
    public function run()
    {
        if ($method = $this->getMethod()) {
            if (!is_callable(array($this, $method))) {
                throw Varien_Exception('Unable to run action method: '.$method);
            }
            
            if ($this->getParam('from')) {
                $container = $this->getProfile()->getContainer($this->getParam('from'));
                $this->setData($container->getData());
            }
            
            $this->$method();
            
            if ($this->getParam('to')) {
                $container = $this->getProfile()->getContainer($this->getParam('to'));
                $container->setData($this->getData());
            }
        } else {
            throw Varien_Exception('No method specified');
        }
        return $this;
    }
    
    public function validateDataGrid()
    {
        $data = $this->getData();
        if (!is_array($data) || !is_array(current($data))) {
            throw Varien_Exception("Invalid data type, expecting 2D grid array.");
        }
        return true;
    }
}