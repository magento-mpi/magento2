<?php
/**
 * API tests adapter interface.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\TestFramework\TestCase\Webapi;

interface AdapterInterface
{
    /**
     * Perform call to the specified service method.
     *
     * @param string $serviceInfo <pre>
     * array(
     *     'rest' => array(
     *         'resourcePath' => $resourcePath, // e.g. /products/:id
     *         'httpMethod' => $httpMethod      // e.g. GET
     *     ),
     *     'soap' => array(
     *         'service' => $soapService,    // soap service name with Version suffix e.g. catalogProductV1, customerV2
     *         'operation' => $operation     // soap operation name e.g. catalogProductCreate
     *     ),
     *     OR
     *     'serviceInterface' => $phpServiceInterfaceName, // e.g. \Magento\Catalog\Service\ProductInterfaceV1
     *     'method' => $serviceMethodName                  // e.g. create
     *     'entityId' => $entityId                         // is used in REST route placeholder (if applicable)
     * );
     * </pre>
     * @param array $arguments
     * @return array
     */
    public function call($serviceInfo, $arguments = array());
}
