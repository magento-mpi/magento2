<?php
/**
 * Application config storage writer interface
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace  Magento\App\Config\Storage;

interface WriterInterface
{
    /**
     * Delete config value from storage
     *
     * @param   string $path
     * @param   string $scope
     * @param   int $scopeId
     * @return void
     */
    public function delete($path, $scope = \Magento\BaseScopeInterface::SCOPE_DEFAULT, $scopeId = 0);

    /**
     * Save config value to storage
     *
     * @param string $path
     * @param string $value
     * @param string $scope
     * @param int $scopeId
     * @return void
     */
    public function save($path, $value, $scope = \Magento\BaseScopeInterface::SCOPE_DEFAULT, $scopeId = 0);
}
