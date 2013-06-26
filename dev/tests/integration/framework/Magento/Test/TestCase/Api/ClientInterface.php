<?php
/**
 * API tests adapter interface.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
interface Magento_Test_TestCase_Api_ClientInterface
{
    /**
     * Perform call to the specified service method.
     *
     * @param string $serviceInfo <pre>
     * array(
     *     'rest' => array('endpoint' => $endpoint, 'httpMethod' => $httpMethod),
     *     'soap' => array('service' => $soapService, 'serviceVersion' => $serviceVersion, 'operation' => $operation),
     *     OR
     *     'serviceInterface' => $phpServiceInterfaceName,
     *     'method' => serviceMethodName
     * );
     * </pre>
     * @param array $arguments
     * @return array
     */
    public function call($serviceInfo, $arguments = array());
}
