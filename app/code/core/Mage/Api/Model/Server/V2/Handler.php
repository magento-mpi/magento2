<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Api
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Webservices server handler v2
 *
 * @category   Mage
 * @package    Mage_Api
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Api_Model_Server_V2_Handler extends Mage_Api_Model_Server_Handler_Abstract
{
    protected $_resourceSuffix = '_V2';

    /**
     * Interceptor for all interfaces
     *
     * @param sttring $function
     * @param array $args
     */

    public function __call( $function, $args )
    {
        $sessionId = array_shift( $args );
        $apiKey = '';
        $nodes = Mage::getSingleton('Mage_Api_Model_Config')->getNode('v2/resources_function_prefix')->children();
        foreach ($nodes as $resource => $prefix) {
            $prefix = $prefix->asArray();
            if (false !== strpos($function, $prefix)) {
                $method = substr($function, strlen($prefix));
                $apiKey = $resource . '.' . strtolower($method[0]).substr($method, 1);
            }
        }
        return $this->call($sessionId, $apiKey, $args);
    }
}
