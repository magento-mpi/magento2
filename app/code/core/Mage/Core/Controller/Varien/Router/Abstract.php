<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

abstract class Mage_Core_Controller_Varien_Router_Abstract
{
    protected $_front;

    public function setFront($front)
    {
        $this->_front = $front;
        return $this;
    }

    public function getFront()
    {
        return $this->_front;
    }

    public function getFrontNameByRoute($routeName)
    {
        return $routeName;
    }

    public function getRouteByFrontName($frontName)
    {
        return $frontName;
    }

    abstract public function match(Zend_Controller_Request_Http $request);
}
