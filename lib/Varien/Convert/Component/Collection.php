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
 * Convert component collection
 *
 * @category   Varien
 * @package    Varien_Convert
 * @author     Moshe Gurvich <moshe@varien.com>
 */
class Varien_Convert_Component_Collection
{
    protected $_components = array();
    protected $_defaultClass;
    
    public function setDefaultClass($className)
    {
        $this->_defaultClass = $className;
        return $this;
    }
    
    public function addComponent($name, $component=null, array $params=array(), $vars=array())
    {
        if (is_null($component)) {
            $component = $this->_defaultClass;
        }
        if (!$component) {
            include_once "Varien/Exception.php";
            throw Varien_Exception("Default Component Class is not set.");
        }
        
        if (is_string($component)) {
            $component = new $component();
        }
        if (!$component instanceof Varien_Convert_Component_Interface) {
            include_once "Varien/Exception.php";
            throw Varien_Exception("Invalid component argument.");
        }
        
        if (is_null($name)) {
            if ($component->getName()) {
                $name = $component->getName();
            } else {
                $name = sizeof($this->_components);   
            }
        }
        if ($params) {
            foreach ($params as $key=>$value) {
                $component->setParam($key, $value);
            }
        }
        if ($vars) {
            foreach ($vars as $key=>$value) {
                $component->setVar($key, $value);
            }
        }
        
        $this->_components[$name] = $component;
        
        return $component;
    }
    
    public function getComponent($name)
    {
        if (!isset($this->_components[$name])) {
            $this->setComponent($name);
        }
        return $this->_components[$name];
    }
}