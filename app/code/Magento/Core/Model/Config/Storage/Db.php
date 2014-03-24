<?php
/**
 * Application config db storage writer
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\Config\Storage;

class Db implements \Magento\App\Config\Storage\WriterInterface
{
    /**
     * Resource model of config data
     *
     * @var \Magento\Core\Model\Resource\Config
     */
    protected $_resource;

    /**
     * @param \Magento\Core\Model\Resource\Config $resource
     */
    public function __construct(\Magento\Core\Model\Resource\Config $resource)
    {
        $this->_resource = $resource;
    }

    /**
     * Delete config value from storage
     *
     * @param   string $path
     * @param   string $scope
     * @param   int $scopeId
     * @return  void
     */
    public function delete($path, $scope = \Magento\App\ScopeInterface::SCOPE_DEFAULT, $scopeId = 0)
    {
        $this->_resource->deleteConfig(rtrim($path, '/'), $scope, $scopeId);
    }

    /**
     * Save config value to storage
     *
     * @param string $path
     * @param string $value
     * @param string $scope
     * @param int $scopeId
     * @return void
     */
    public function save($path, $value, $scope = \Magento\App\ScopeInterface::SCOPE_DEFAULT, $scopeId = 0)
    {
        $this->_resource->saveConfig(rtrim($path, '/'), $value, $scope, $scopeId);
    }
}
