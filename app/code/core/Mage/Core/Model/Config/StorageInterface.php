<?php

interface Mage_Core_Model_Config_StorageInterface
{
    /**
     * Read additional file during initialization
     */
    const INIT_OPTION_EXTRA_FILE = 'MAGE_CONFIG_FILE';

    /**
     * Read additional data (XML-string) during initialization
     */
    const INIT_OPTION_EXTRA_DATA = 'MAGE_CONFIG_DATA';

    /**
     * Local configuration file
     */
    const LOCAL_CONFIG_FILE = 'local.xml';

    /**
     * Get loaded configuration
     *
     * @param bool $useCache
     * @return mixed
     */
    public function getConfiguration($useCache = true);

    /**
     * Iterate all active modules "etc" folders and combine data from
     * specified xml file name to one object
     *
     * @param   string $fileName
     * @param   null|Mage_Core_Model_Config_Base $mergeToObject
     * @param   mixed $mergeModel
     * @return  Mage_Core_Model_Config_Base
     */
    public function loadModulesConfiguration($fileName, $mergeToObject = null, $mergeModel = null);

    /**
     * Go through all modules and find configuration files of active modules
     *
     * @param string $filename
     * @return array
     */
    public function getModuleConfigurationFiles($filename);

    /**
     * Get resource configuration for resource name
     *
     * @param string $name
     * @return Varien_Simplexml_Element
     */
    public function getResourceConfig($name);

    /**
     * Get connection configuration
     *
     * @param   string $name
     * @return  Varien_Simplexml_Element
     */
    public function getResourceConnectionConfig($name);

    /**
     * Retrieve resource connection model name
     *
     * @param string $moduleName
     * @return string
     */
    public function getResourceConnectionModel($moduleName = null);

    /**
     * Remove configuration cache
     * @param array $tags
     */
    public function removeCache(array $tags);
}
