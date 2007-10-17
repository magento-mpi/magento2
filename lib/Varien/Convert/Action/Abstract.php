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
 * Convert action abstract
 *
 * @category   Varien
 * @package    Varien_Convert
 * @author     Moshe Gurvich <moshe@varien.com>
 */
abstract class Varien_Convert_Action_Abstract implements Varien_Convert_Action_Interface
{
    protected $_params;
    protected $_profile;
    protected $_container;
    
    public function getParam($key, $default=null)
    {
        if (!isset($this->_params[$key])) {
            return $default;
        }
        return $this->_params[$key];
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
        
    public function getParams()
    {
        return $this->_params;
    }
    
    public function setParams($params)
    {
        $this->_params = $params;
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
        
    public function getContainer($name=null)
    {
        if (!is_null($name)) {
            return $this->getProfile()->getContainer($name);
        }
        
        if (!$this->_container) {
            $class = $this->getParam('class');
            $this->_container = new $class();
            $this->_container->setProfile($this->getProfile());
        }
        return $this->_container;
    }
    
    public function run()
    {
        if ($method = $this->getParam('method')) {
            if (!is_callable(array($this->getContainer(), $method))) {
                throw Varien_Exception('Unable to run action method: '.$method);
            }
            
            if ($this->getParam('from')) {
                $this->getContainer()->setData($this->getContainer($this->getParam('from'))->getData());
            }
            
            $this->getContainer()->$method();
            
            if ($this->getParam('to')) {
                $this->getContainer($this->getParam('to'))->setData($this->getContainer()->getData());
            }
        } else {
            throw Varien_Exception('No method specified');
        }
        return $this;
    }

}