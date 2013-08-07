<?php
use Zend\Soap\Wsdl;

/**
 * Auto discovery tool for WSDL generation from Magento web API configuration.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webapi_Model_Soap_AutoDiscover
{
    /**
     * Cache ID for generated WSDL content.
     */
    const WSDL_CACHE_ID = 'WSDL';

    /** @var Mage_Webapi_Model_Soap_Wsdl_Generator */
    protected $_wsdlGenerator;

    /** @var Mage_Core_Model_CacheInterface */
    protected $_cache;

    /**
     * Construct auto discover with service config and list of requested services.
     *
     * @param Mage_Core_Model_CacheInterface $cache
     * @param Mage_Webapi_Model_Soap_Wsdl_Generator $wsdlGenerator
     *
     * @throws InvalidArgumentException
     */
    public function __construct(
        Mage_Core_Model_CacheInterface $cache,
        Mage_Webapi_Model_Soap_Wsdl_Generator $wsdlGenerator
    ) {
        $this->_cache = $cache;
        $this->_wsdlGenerator = $wsdlGenerator;
    }

    /**
     * Generate WSDL content and save it to cache.
     *
     * @param array $requestedServices
     * @param string $endpointUrl
     * @return string
     * @throws Mage_Webapi_Exception
     */
    public function handle($requestedServices, $endpointUrl)
    {
        /** TODO: Uncomment caching */
        /* $cacheId = self::WSDL_CACHE_ID . hash('md5', serialize($requestedServices));
        if ($this->_cache->canUse(Mage_Webapi_Model_ConfigAbstract::WEBSERVICE_CACHE_NAME)) {
            $cachedWsdlContent = $this->_cache->load($cacheId);
            if ($cachedWsdlContent !== false) {
                return $cachedWsdlContent;
            }
        }*/

        $wsdlContent = $this->_wsdlGenerator->generate($requestedServices, $endpointUrl);

        /* if ($this->_cache->canUse(Mage_Webapi_Model_ConfigAbstract::WEBSERVICE_CACHE_NAME)) {
            $this->_cache->save($wsdlContent, $cacheId, array(Mage_Webapi_Model_ConfigAbstract::WEBSERVICE_CACHE_TAG));
        }*/

        return $wsdlContent;
    }
}
