<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Service\V1;

class FileResolverStub implements \Magento\Framework\Config\FileResolverInterface
{
    /**
     * {@inheritdoc}
     */
    public function get($filename, $scope)
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $readDirectory = $objectManager->create(
            'Magento\Framework\Filesystem\Directory\Read',
            [
                'config' => ['path' => realpath(__DIR__ . '/../../_files/etc')],
                'driver' => $objectManager->create('Magento\Framework\Filesystem\Driver\File')
            ]
        );
        $paths = ['data_object.xml'];
        return new \Magento\Framework\Config\FileIterator($readDirectory, $paths);
    }
}
