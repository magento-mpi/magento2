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
        $models = Mage_ImportExport_Model_Config::getModels(Mage_ImportExport_Model_Export::CONFIG_KEY_FORMATS);

        if (isset($models[$modelName]['model'])) {
            return $this->_objectManager->create($models[$modelName]['model'], array('destination' => $destination));
        }
        throw new Exception('Invalid export storage adapter');
    }
}
