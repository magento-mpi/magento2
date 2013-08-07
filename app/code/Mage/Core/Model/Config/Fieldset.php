<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Core_Model_Config_Fieldset extends Mage_Core_Model_Config_Base
{
    /**
     * Constructor.
     * Load configuration from enabled modules with appropriate caching.
     *
     * @param Mage_Core_Model_Config_Modules_Reader $configReader
     * @param Mage_Core_Model_Cache_Type_Config $configCacheType
     * @param Magento_Simplexml_Element|string|null $data
     */
    public function __construct(
        Mage_Core_Model_Config_Modules_Reader $configReader,
        Mage_Core_Model_Cache_Type_Config $configCacheType,
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
