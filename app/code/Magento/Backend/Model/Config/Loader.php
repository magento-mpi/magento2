<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * System configuration loader
 */
namespace Magento\Backend\Model\Config;

class Loader
{
    /**
     * Config data factory
     *
     * @var \Magento\Framework\App\Config\ValueFactory
     */
    protected $_configValueFactory;

    /**
     * @param \Magento\Framework\App\Config\ValueFactory $configValueFactory
     */
    public function __construct(\Magento\Framework\App\Config\ValueFactory $configValueFactory)
    {
        $this->_configValueFactory = $configValueFactory;
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
        $configDataCollection = $this->_configValueFactory->create();
        $configDataCollection = $configDataCollection->getCollection()->addScopeFilter($scope, $scopeId, $path);

        $config = array();
        $configDataCollection->load();
        foreach ($configDataCollection->getItems() as $data) {
            if ($full) {
                $config[$data->getPath()] = array(
                    'path' => $data->getPath(),
                    'value' => $data->getValue(),
                    'config_id' => $data->getConfigId()
                );
            } else {
                $config[$data->getPath()] = $data->getValue();
            }
        }
        return $config;
    }
}
