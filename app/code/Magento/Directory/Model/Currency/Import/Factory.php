<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Directory_Model_Currency_Import_Factory
{
    /**
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * @var Magento_Directory_Model_Currency_Import_Config
     */
    protected $_serviceConfig;

    /**
     * @param Magento_ObjectManager $objectManager
     * @param Magento_Directory_Model_Currency_Import_Config $serviceConfig
     */
    public function __construct(
        Magento_ObjectManager $objectManager,
        Magento_Directory_Model_Currency_Import_Config $serviceConfig
    ) {
        $this->_objectManager = $objectManager;
        $this->_serviceConfig = $serviceConfig;
    }

    /**
     * Create new import object
     *
     * @param string $serviceName
     * @param array $data
     * @throws InvalidArgumentException
     * @throws UnexpectedValueException
     * @return Magento_Directory_Model_Currency_Import_Interface
     */
    public function create($serviceName, array $data = array())
    {
        $serviceClass = $this->_serviceConfig->getServiceClass($serviceName);
        if (!$serviceClass) {
            throw new InvalidArgumentException("Currency import service '$serviceName' is not defined.");
        }
        $serviceInstance = $this->_objectManager->create($serviceClass, $data);
        if (!($serviceInstance instanceof Magento_Directory_Model_Currency_Import_Interface)) {
            throw new UnexpectedValueException(
                "Class '$serviceClass' has to implement Magento_Directory_Model_Currency_Import_Interface."
            );
        }
        return $serviceInstance;
    }
}
