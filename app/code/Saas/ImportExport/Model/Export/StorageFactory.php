<?php
/**
 * Export Storage Factory
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_ImportExport_Model_Export_StorageFactory
{
    /**
     * Constructor
     *
     * @param Magento_ObjectManager $objectManager
     */
    public function __construct(Magento_ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Return concrete storage instance
     *
     * @param string $storageFormat
     * @param string $destination
     * @return Saas_ImportExport_Model_Export_Adapter_Abstract
     * @throws Exception
     */
    public function create($storageFormat, $destination)
    {
        $validStorages = Mage_ImportExport_Model_Config::getModels(Mage_ImportExport_Model_Export::CONFIG_KEY_FORMATS);

        if (isset($validStorages[$storageFormat])) {
            return $this->_objectManager->create($validStorages[$storageFormat]['model'],
                array('destination' => $destination));
        }
        throw new Exception('Invalid export storage adapter');
    }
}
