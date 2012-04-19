<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_PHPUnit
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * TestCase for controllers
 *
 * @category    Mage
 * @package     Mage_PHPUnit
 * @author      Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_PHPUnit_ControllerTestCase extends Mage_PHPUnit_TestCase
{
    /**
     * Prepares initializers.
     * Is called in setUp method first. Can be overridden in testCases to add more initializers.
     */
    protected function prepareInitializers()
    {
        parent::prepareInitializers();
        Mage_PHPUnit_Initializer_Factory::createInitializer('Mage_PHPUnit_Initializer_HeadersAlreadySent')
            ->setThrowException(false);
    }

    /**
     * Get request object
     *
     * @return Mage_Core_Controller_Request_Http
     */
    protected function getRequest()
    {
        return Mage::app()->getRequest();
    }

    /**
     * Get response object
     *
     * @return Mage_Core_Controller_Response_Http
     */
    protected function getResponse()
    {
        return Mage::app()->getResponse();
    }
}
