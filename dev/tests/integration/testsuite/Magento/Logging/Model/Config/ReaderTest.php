<?php
/**
 * Magento_Logging_Model_Config_Reader
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 * @magentoDataFixture Magento/Adminhtml/controllers/_files/cache/all_types_disabled.php
 */
namespace Magento\Logging\Model\Config;

class ReaderTest extends \PHPUnit_Framework_TestCase
{

    public function testRead()
    {
        /** @var \Magento\Core\Model\Dir $dirs */
        $dirs = \Mage::getObjectManager()->create(
            'Magento\Core\Model\Dir', array(
                'baseDir' => BP,
                'dirs' => array(
                    \Magento\Core\Model\Dir::MODULES => __DIR__ . '/_files',
                    \Magento\Core\Model\Dir::CONFIG => __DIR__ . '/_files'
                )
            )
        );

        /** @var Magento\Core\Model\Module\Declaration\FileResolver $modulesDeclarations */
        $modulesDeclarations = \Mage::getObjectManager()->create(
            'Magento\Core\Model\Module\Declaration\FileResolver', array(
                'applicationDirs' => $dirs,
            )
        );


        /** @var Magento\Core\Model\Module\Declaration\Reader\Filesystem $filesystemReader */
        $filesystemReader = \Mage::getObjectManager()->create(
            'Magento\Core\Model\Module\Declaration\Reader\Filesystem', array(
                'fileResolver' => $modulesDeclarations,
            )
        );

        /** @var \Magento\Core\Model\ModuleList $modulesList */
        $modulesList = \Mage::getObjectManager()->create(
            'Magento\Core\Model\ModuleList', array(
                'reader' => $filesystemReader,
            )
        );

        /** @var \Magento\Core\Model\Config\Modules\Reader $moduleReader */
        $moduleReader = \Mage::getObjectManager()->create(
            'Magento\Core\Model\Config\Modules\Reader', array(
                'dirs' => $dirs,
                'moduleList' => $modulesList
            )
        );

        /** @var \Magento\Core\Model\Config\FileResolver $fileResolver */
        $fileResolver = \Mage::getObjectManager()->create(
            'Magento\Core\Model\Config\FileResolver', array(
                'moduleReader' => $moduleReader,
            )
        );

        /** @var \Magento\Logging\Model\Config\Reader $model */
        $model = \Mage::getObjectManager()->create(
            'Magento\Logging\Model\Config\Reader', array(
                'fileResolver' => $fileResolver,
            )
        );

        $result = $model->read('global');
        $expected = include '_files/expectedArray.php';
        $this->assertEquals($expected, $result);
    }

    public function testMergeCompleteAndPartial()
    {
        $fileList = array(
            __DIR__ . '/_files/customerBalance.xml',
            __DIR__ . '/_files/Reward.xml'
        );
        $fileResolverMock = $this->getMockBuilder('Magento\Config\FileResolverInterface')
            ->setMethods(array('get'))
            ->disableOriginalConstructor()
            ->getMock();
        $fileResolverMock->expects($this->once())
            ->method('get')
            ->with($this->equalTo('logging.xml'), $this->equalTo('global'))
            ->will($this->returnValue($fileList));

        /** @var \Magento\Logging\Model\Config\Reader $model */
        $model = \Mage::getObjectManager()->create(
            'Magento\Logging\Model\Config\Reader', array(
                'fileResolver' => $fileResolverMock,
            )
        );
        $this->assertArrayHasKey('logging', $model->read('global'));
    }
}
