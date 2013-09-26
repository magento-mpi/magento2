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
        /** @var \Magento\Core\Model\Dir $dirs */
        $dirs = $objectManager->create(
            'Magento\Core\Model\Dir', array(
                'baseDir' => BP,
                'dirs' => array(
                    \Magento\Core\Model\Dir::MODULES => __DIR__ . '/_files',
                    \Magento\Core\Model\Dir::CONFIG => __DIR__ . '/_files'
                )
            )
        );

        /** @var \Magento\Core\Model\Module\Declaration\FileResolver $modulesDeclarations */
        $modulesDeclarations = $objectManager->create(
            'Magento\Core\Model\Module\Declaration\FileResolver', array(
                'applicationDirs' => $dirs,
            )
        );


        /** @var \Magento\Core\Model\Module\Declaration\Reader\Filesystem $filesystemReader */
        $filesystemReader = $objectManager->create(
            'Magento\Core\Model\Module\Declaration\Reader\Filesystem', array(
                'fileResolver' => $modulesDeclarations,
            )
        );

        /** @var \Magento\Core\Model\ModuleList $modulesList */
        $modulesList = $objectManager->create(
            'Magento\Core\Model\ModuleList', array(
                'reader' => $filesystemReader,
            )
        );

        /** @var \Magento\Core\Model\Config\Modules\Reader $moduleReader */
        $moduleReader = $objectManager->create(
            'Magento\Core\Model\Config\Modules\Reader', array(
                'dirs' => $dirs,
                'moduleList' => $modulesList
            )
        );

        /** @var \Magento\Core\Model\Config\FileResolver $fileResolver */
        $fileResolver = $objectManager->create(
            'Magento\Core\Model\Config\FileResolver', array(
                'moduleReader' => $moduleReader,
            )
        );

        /** @var \Magento\Logging\Model\Config\Reader $model */
        $model = $objectManager->create(
            'Magento\GiftRegistry\Model\Config\Reader', array(
                'fileResolver' => $fileResolver,
            )
        );

        $result = $model->read('global');
        $expected = include '_files/giftregistry_config.php';
        $this->assertEquals($expected, $result);
    }
}
