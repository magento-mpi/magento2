<?php
/**
 * Entity Export Factory
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_ImportExport_Model_Export_EntityFactory
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
     * Return concrete entity instance
     *
     * @param string $entityType
     * @param array $params
     * @return Saas_ImportExport_Model_Export_EntityInterface
     * @throws Exception
     */
    public function create($entityType, $params)
    {
        $entityTypes = Mage_ImportExport_Model_Config::getModels(Mage_ImportExport_Model_Export::CONFIG_KEY_ENTITIES);
        if (isset($entityTypes[$entityType])) {
            $entity = $this->_objectManager->create($entityTypes[$entityType]['model']);
            if (!$entity instanceof Saas_ImportExport_Model_Export_EntityInterface) {
                throw new Exception('Invalid export entity model');
            }
            $entity->setParameters($params);
            return $entity;
        }
        throw new Exception('Invalid export entity model');
    }
}
