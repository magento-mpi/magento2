<?php
/**
 * Module declaration file resolver. Produces list of composer.json files from module, config, and custom directories.
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Framework\Module\Declaration\FileResolver;

class ComposerJson extends \Magento\Framework\Module\Declaration\FileResolver
{
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function get($filename, $scope)
    {
        $moduleDir = $this->modulesDirectory->getAbsolutePath();

        $mageScopePath = $moduleDir . '/Magento';
        $output = ['mage' => [], 'custom' => []];
        $files = glob($moduleDir . '*/*/composer.json');
        if (!empty($files)) {
            foreach ($files as $file) {
                $scope = strpos($file, $mageScopePath) === 0 ? 'mage' : 'custom';
                $output[$scope][] = $this->rootDirectory->getRelativePath($file);
            }
        }
        return $this->iteratorFactory->create(
            $this->rootDirectory,
            array_merge($output['mage'], $output['custom'])
        );
    }
}
