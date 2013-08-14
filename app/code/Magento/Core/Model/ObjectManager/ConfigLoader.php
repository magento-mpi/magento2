<?php
/**
 * ObjectManager configuration loader
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Core_Model_ObjectManager_ConfigLoader
{
    /**
     * Modules reader
     *
     * @var Magento_Core_Model_Config_Modules_Reader
     */
    protected $_modulesReader;

    /**
     * Config reader factory
     *
     * @var Magento_ObjectManager_Config_Reader_DomFactory
     */
    protected $_readerFactory;

    /**
     * Cache
     *
     * @var Magento_Cache_FrontendInterface
     */
    protected $_cache;

    /**
     * Application mode
     *
     * @var string
     */
    protected $_appMode;

    /**
     * @param Magento_Cache_FrontendInterface $cache
     * @param Magento_Core_Model_Config_Modules_Reader $modulesReader
     * @param Magento_ObjectManager_Config_Reader_DomFactory $readerFactory
     * @param string $mode
     */
    public function __construct(
        Magento_Cache_FrontendInterface $cache,
        Magento_Core_Model_Config_Modules_Reader $modulesReader,
        Magento_ObjectManager_Config_Reader_DomFactory $readerFactory,
        $mode = Magento_Core_Model_App_State::MODE_DEFAULT
    ) {
        $this->_cache = $cache;
        $this->_modulesReader = $modulesReader;
        $this->_readerFactory = $readerFactory;
        $this->_appMode = $mode;
    }

    /**
     * Load modules DI configuration
     *
     * @param string $area
     * @return array
     */
    public function load($area)
    {
        $key = $area . 'DiConfig';
        $data = $this->_cache->load($key);
        if ($data) {
            $result = unserialize($data);
        } else {
            $fileType = $area !== 'global' ? ($area . DIRECTORY_SEPARATOR . 'di.xml') : 'di.xml';
            $files = $this->_modulesReader->getModuleConfigurationFiles($fileType);
            /** @var Magento_ObjectManager_Config_Reader_Dom $reader */
            $reader = $this->_readerFactory->create(
                array(
                    'configFiles' => $files,
                    'isRuntimeValidated' => $this->_appMode == Magento_Core_Model_App_State::MODE_DEVELOPER
                )
            );
            $result = $reader->read();
            $this->_cache->save(serialize($result), $key);
        }
        return $result;
    }
}
