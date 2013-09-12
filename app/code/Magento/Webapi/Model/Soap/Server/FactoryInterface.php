<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
interface Magento_Webapi_Model_Soap_Server_FactoryInterface
{
    /**
     * Create SoapServer
     *
     * @param string $url Soap endpoint URL
     * @param array $options Options including encoding, soap_version etc
     * @param object $handler Handler object to handle soap requests
     * @return SoapServer
     */
    public function create($url, $options, $handler);
}