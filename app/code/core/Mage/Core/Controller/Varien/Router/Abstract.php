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
     * Retrieve front controller instance
     *
     * @return Mage_Core_Controller_Varien_Front
     */
    public function getFront()
    {
        return Mage::app()->getFrontController();
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
