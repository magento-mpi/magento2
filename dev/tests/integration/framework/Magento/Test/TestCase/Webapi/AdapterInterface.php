<?php
/**
 * API tests adapter interface.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
interface Magento_Test_TestCase_Webapi_AdapterInterface
{
    /**
     * Perform call to the specified service method.
     *
     * @param string $serviceInfo <pre>
     * array(
     *     'rest' => array(
     *         'endpoint' => $endpoint,    // e.g. /products/:id
     *         'httpMethod' => $httpMethod // e.g. GET
     *     ),
     *     'soap' => array(
     *         'service' => $soapService,           // soap service name e.g. catalogProduct, customer
     *         'serviceVersion' => $serviceVersion, // with 'V' prefix or without it
     *         'operation' => $operation            // soap operation name e.g. catalogProductCreate
     *     ),
     *     OR
     *     'serviceInterface' => $phpServiceInterfaceName, // e.g. Mage_Catalog_Service_ProductInterfaceV1
     *     'method' => serviceMethodName                   // e.g. create
     * );
     * </pre>
     * @param array $arguments
     * @return array
     */
    public function call($serviceInfo, $arguments = array());
}
