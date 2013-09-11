<?php
/**
 * Application config storage writer interface
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\Config\Storage;

interface WriterInterface
{
    /**
     * Delete config value from storage
     *
     * @param   string $path
     * @param   string $scope
     * @param   int $scopeId
     */
    public function delete($path, $scope = \Magento\Core\Model\Store::DEFAULT_CODE, $scopeId = 0);

    /**
     * Save config value to storage
     *
     * @param string $path
     * @param string $value
     * @param string $scope
     * @param int $scopeId
     */
    public function save($path, $value, $scope = \Magento\Core\Model\Store::DEFAULT_CODE, $scopeId = 0);
}
