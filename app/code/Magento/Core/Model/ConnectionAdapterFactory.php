<?php
/**
 * Connection adapter factory
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Core_Model_ConnectionAdapterFactory
{
    /**
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * @param Magento_ObjectManager $objectManager
     */
    public function __construct(Magento_ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Create connection adapter instance
     *
     * @param string $adapterInstanceName
     * @param array $connectionParams
     * @return Magento_Core_Model_Resource_ConnectionAdapterInterface
     * @throws Exception
     */
    public function create($adapterInstanceName, array $connectionParams = array())
    {
        $adapterInstance = $this->_objectManager->create($adapterInstanceName, $connectionParams);

        if ($adapterInstance instanceof Magento_Core_Model_Resource_ConnectionAdapterInterface) {
            throw Exception('Trying to create wrong connection adapter');
        }

        return $adapterInstance;
    }
}
