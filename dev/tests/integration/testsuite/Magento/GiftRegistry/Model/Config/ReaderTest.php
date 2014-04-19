<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftRegistry
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftRegistry\Model\Config;

class ReaderTest extends \PHPUnit_Framework_TestCase
{
    public function testRead()
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        /** @var \Magento\Framework\App\Filesystem $filesystem */
        $filesystem = $objectManager->create(
            'Magento\Framework\App\Filesystem',
            array(
                'directoryList' => $objectManager->create(
                    'Magento\Framework\App\Filesystem\DirectoryList',
                    array(
                        'root' => BP,
                        'directories' => array(
                            \Magento\Framework\App\Filesystem::MODULES_DIR => array('path' => __DIR__ . '/_files'),
                            \Magento\Framework\App\Filesystem::CONFIG_DIR => array('path' => __DIR__ . '/_files')
                        )
                    )
                )
            )
        );

        $moduleDirs = $objectManager->create('Magento\Module\Dir', array('filesystem' => $filesystem));

        /** @var \Magento\Module\Dir\Reader $moduleReader */
        $moduleReader = $objectManager->create(
            'Magento\Module\Dir\Reader',
            array('moduleDirs' => $moduleDirs, 'filesystem' => $filesystem)
        );

        /** @var \Magento\Framework\App\Config\FileResolver $fileResolver */
        $fileResolver = $objectManager->create(
            'Magento\Framework\App\Config\FileResolver',
            array('moduleReader' => $moduleReader)
        );

        /** @var \Magento\Logging\Model\Config\Reader $model */
        $model = $objectManager->create(
            'Magento\GiftRegistry\Model\Config\Reader',
            array('fileResolver' => $fileResolver)
        );

        $result = $model->read('global');
        $expected = include '_files/giftregistry_config.php';
        $this->assertEquals($expected, $result);
    }
}
