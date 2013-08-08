<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Core_Model_Config_Fieldset extends Magento_Core_Model_Config_Base
{
    /**
     * Constructor.
     * Load configuration from enabled modules with appropriate caching.
     *
     * @param Magento_Core_Model_Config_Modules_Reader $configReader
     * @param Magento_Core_Model_Cache_Type_Config $configCacheType
     * @param Magento_Simplexml_Element|string|null $data
     */
    public function __construct(
        Magento_Core_Model_Config_Modules_Reader $configReader,
        Magento_Core_Model_Cache_Type_Config $configCacheType,
        $data = null
    ) {
        parent::__construct($data);
        $cacheId = 'fieldset_config';
        $cachedXml = $configCacheType->load($cacheId);
        if ($cachedXml) {
            $this->loadString($cachedXml);
        } else {
            $config = $configReader->loadModulesConfiguration('fieldset.xml');
            $xmlConfig = $config->getNode();
            $configCacheType->save($xmlConfig->asXML(), $cacheId);
            $this->setXml($xmlConfig);
        }
    }
}
