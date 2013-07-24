<?php
use Zend\Server\Reflection\ReflectionMethod;

/**
 * Web API configuration.
 *
 * This class is responsible for collecting web API configuration using reflection
 * as well as for implementing interface to provide access to collected configuration.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
abstract class Mage_Webapi_Model_ConfigAbstract
{
    /**#@+
     * Cache parameters.
     */
    const WEBSERVICE_CACHE_NAME = Mage_Webapi_Model_Cache_Type::TYPE_IDENTIFIER;
    const WEBSERVICE_CACHE_TAG = Mage_Webapi_Model_Cache_Type::CACHE_TAG;
    /**#@-*/

    /**#@+
     * Version parameters.
     */
    const VERSION_NUMBER_PREFIX = 'V';
    const VERSION_MIN = 1;
    /**#@-*/
}
