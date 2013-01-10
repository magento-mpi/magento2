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
     * @param Mage_Core_Model_Config_StorageInterface $configStorage
     * @param Varien_Simplexml_Element|string|null $data
     */
    public function __construct(Mage_Core_Model_Config_StorageInterface $configStorage, $data = null)
    {
        parent::__construct($data);

        $canUseCache = Mage::app()->useCache('config');
        if ($canUseCache) {
            /* Setup caching with no checksum validation */
            $this->setCache(Mage::app()->getCache())
                ->setCacheChecksum(null)
                ->setCacheId('fieldset_config')
                ->setCacheTags(array(Mage_Core_Model_Config::CACHE_TAG));
            if ($this->loadCache()) {
                return;
            }
        }

        $config = $configStorage->loadModulesConfiguration('fieldset.xml');
        $this->setXml($config->getNode());

        if ($canUseCache) {
            $this->saveCache();
        }
    }
}
