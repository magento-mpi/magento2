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
     * @param string $modelName
     * @param string $destination
     * @return Saas_ImportExport_Model_Export_Adapter_AdapterAbstract
     * @throws Exception
     */
    public function create($modelName, $destination)
    {
        $models = Magento_ImportExport_Model_Config::getModels(Magento_ImportExport_Model_Export::CONFIG_KEY_FORMATS);

        if (isset($models[$modelName]['model'])) {
            $storage = $this->_objectManager->create($models[$modelName]['model'],
                array('destination' => $destination));
            if (!$storage instanceof Saas_ImportExport_Model_Export_Adapter_AdapterAbstract) {
                throw new Exception('Invalid export storage adapter');
            }
            return $storage;
        }
        throw new Exception('Invalid export storage adapter');
    }
}
