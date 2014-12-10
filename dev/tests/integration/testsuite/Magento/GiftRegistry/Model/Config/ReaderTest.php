<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\GiftRegistry\Model\Config;

use Magento\Framework\App\Filesystem\DirectoryList;

class ReaderTest extends \PHPUnit_Framework_TestCase
{
    public function testRead()
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        /** @var \Magento\Framework\Filesystem $filesystem */
        $filesystem = $objectManager->create(
            'Magento\Framework\Filesystem',
            [
                'directoryList' => $objectManager->create(
                    'Magento\Framework\App\Filesystem\DirectoryList',
                    [
                        'root' => BP,
                        'config' => [
                            DirectoryList::MODULES => [DirectoryList::PATH => __DIR__ . '/_files'],
                            DirectoryList::CONFIG => [DirectoryList::PATH => __DIR__ . '/_files'],
                        ]
                    ]
                )
            ]
        );

        $moduleDirs = $objectManager->create('Magento\Framework\Module\Dir', ['filesystem' => $filesystem]);

        /** @var \Magento\Framework\Module\Dir\Reader $moduleReader */
        $moduleReader = $objectManager->create(
            'Magento\Framework\Module\Dir\Reader',
            ['moduleDirs' => $moduleDirs, 'filesystem' => $filesystem]
        );

        /** @var \Magento\Framework\App\Config\FileResolver $fileResolver */
        $fileResolver = $objectManager->create(
            'Magento\Framework\App\Config\FileResolver',
            ['moduleReader' => $moduleReader]
        );

        /** @var \Magento\Logging\Model\Config\Reader $model */
        $model = $objectManager->create(
            'Magento\GiftRegistry\Model\Config\Reader',
            ['fileResolver' => $fileResolver]
        );

        $result = $model->read('global');
        $expected = include '_files/giftregistry_config.php';
        $this->assertEquals($expected, $result);
    }
}
