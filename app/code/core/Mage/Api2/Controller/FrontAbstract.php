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
    // TODO: Think about empty implementation instead of abstract
    abstract public function init();

    /**
     * Dispatch request and send response
     *
     * @return Mage_Api2_Controller_FrontAbstract
     */
    // TODO: Think about implementing this method like 'template method' instead of abstract
    abstract public function dispatch();

    /**
     * Retrieve config describing resources available in all APIs
     * The same resource config must be used in all API types
     */
    final protected function _getResourceConfig()
    {
        // TODO: Implement
    }

    /**
     * Check permissions on specific resource in ACL. No information about roles must be used on this level.
     * ACL check must be performed in the same way for all API types
     */
    final protected function _checkResourceAcl()
    {
        // TODO: Implement
    }

    /**
     * Retrieve request object
     *
     * TODO: Check return type
     * @return Mage_Core_Controller_Request_Http
     */
    protected function _getRequest()
    {
        return Mage::app()->getRequest();
    }

    /**
     * Retrieve response object
     *
     * TODO: Check return type
     * @return Zend_Controller_Response_Http
     */
    protected function _getResponse()
    {
        return Mage::app()->getResponse();
    }
}
