<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Abstract router class
 */
abstract class Mage_Core_Controller_Varien_Router_Abstract
{
    /**
     * @var Mage_Core_Controller_Varien_Front
     */
    protected $_front;

    /**
     * Assign front controller instance
     *
     * @param $front Mage_Core_Controller_Varien_Front
     * @return Mage_Core_Controller_Varien_Router_Abstract
     */
    public function setFront(Mage_Core_Controller_Varien_Front $front)
    {
        $this->_front = $front;
        return $this;
    }

    /**
     * Retrieve front controller instance
     *
     * @return Mage_Core_Controller_Varien_Front
     */
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
