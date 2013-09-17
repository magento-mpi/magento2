<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Directory
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Import currency model factory
 */
class Magento_Directory_Model_Currency_Import_Factory
{
    /**
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * @var Magento_Core_Model_Config
     */
    protected $_coreConfig;

    /**
     * @param Magento_ObjectManager $objectManager
     * @param Magento_Core_Model_Config $coreConfig
     */
    public function __construct(
        Magento_ObjectManager $objectManager,
        Magento_Core_Model_Config $coreConfig
    ) {
        $this->_objectManager = $objectManager;
        $this->_coreConfig = $coreConfig;
    }

    /**
     * Create new import object
     *
     * @param $service
     * @param array $data
     * @throws InvalidArgumentException
     * @return Magento_Directory_Model_Currency_Import_Interface
     */
    public function create($service, array $data = array())
    {
        $serviceClass = $this->_coreConfig->getValue('global/currency/import/services/' . $service . '/model');
        $service = $this->_objectManager->create($serviceClass, $data);
        if (false == ($service instanceof Magento_Directory_Model_Currency_Import_Interface)) {
            throw new InvalidArgumentException(
                $serviceClass . ' doesn\'t implement Magento_Directory_Model_Currency_Import_Interface'
            );
        }
        return $service;
    }
}
