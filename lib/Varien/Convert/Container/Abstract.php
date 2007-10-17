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
 * Convert container abstract
 *
 * @category   Varien
 * @package    Varien_Convert
 * @author     Moshe Gurvich <moshe@varien.com>
 */
abstract class Varien_Convert_Container_Abstract implements Varien_Convert_Container_Interface
{
    protected $_vars;
    protected $_profile;
    protected $_data;

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
            $this->_data = $this->getProfile()->getContainer()->getData();
        }
        return $this->_data;
    }
        
    public function setData($data)
    {
        if ($this->getProfile()) {
            $this->getProfile()->getContainer()->setData($data);
        }
        $this->_data = $data;
        return $this;   
    }

    public function validateDataString()
    {
        $data = $this->getData();
        if (!is_string($data)) {
            throw Varien_Exception("Invalid data type, expecting string.");
        }
        return true;
    }

    public function validateDataArray()
    {
        $data = $this->getData();
        if (!is_array($data)) {
            throw Varien_Exception("Invalid data type, expecting array.");
        }
        return true;
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