<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * System configuration loader
 */
class Magento_Backend_Model_Config_Loader
{
    /**
     * Config data factory
     *
     * @var Magento_Core_Model_Config_DataFactory
     */
    protected $_configDataFactory;

    /**
     * @param Magento_Core_Model_Config_DataFactory $configDataFactory
     */
    public function __construct(Magento_Core_Model_Config_DataFactory $configDataFactory)
    {
        $this->_configDataFactory = $configDataFactory;
    }

    /**
     * Get configuration value by path
     *
     * @param string $path
     * @param string $scope
     * @param string $scopeId
     * @param bool $full
     * @return array
     */
    public function getConfigByPath($path, $scope, $scopeId, $full = true)
    {
        $configDataCollection = $this->_configDataFactory->create();
        $configDataCollection = $configDataCollection
            ->getCollection()
            ->addScopeFilter($scope, $scopeId, $path);

        $config = array();
        $configDataCollection->load();
        foreach ($configDataCollection->getItems() as $data) {
            if ($full) {
                $config[$data->getPath()] = array(
                    'path'      => $data->getPath(),
                    'value'     => $data->getValue(),
                    'config_id' => $data->getConfigId()
                );
            } else {
                $config[$data->getPath()] = $data->getValue();
            }
        }
        return $config;
    }
}
