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
 * Generic front controller for all API areas
 */
abstract class Mage_Api2_Controller_FrontAbstract
{
    /**
     * Initialize front controller
     *
     * @return Mage_Core_Controller_Varien_Front
     */
    abstract public function init();

    /**
     * Dispatch request and send response
     *
     * @return Mage_Api2_Controller_FrontAbstract
     */
    abstract public function dispatch();

    /**
     * Retrieve request object
     *
     * @return Mage_Core_Controller_Request_Http
     */
    public function getRequest()
    {
        return Mage::app()->getRequest();
    }

    /**
     * Retrieve response object
     *
     * @return Zend_Controller_Response_Http
     */
    public function getResponse()
    {
        return Mage::app()->getResponse();
    }
}
