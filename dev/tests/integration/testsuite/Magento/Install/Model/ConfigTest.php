<?php
/**
 * \Magento\Install\Model\Config
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Install\Model;

use Magento\Framework\App\Filesystem\DirectoryList;

class ConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Install\Model\Config
     */
    private $_object;

    /**
     * @var \Magento\Framework\ObjectManager
     */
    private $_objectManager;

    public function setUp()
    {
        $this->_objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        /** @var $cacheTypeList \Magento\Framework\App\Cache\TypeListInterface */
        $cacheTypeList = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Framework\App\Cache\TypeListInterface'
        );
        $types = array_keys($cacheTypeList->getTypes());

        /** @var $cacheState \Magento\Framework\App\Cache\StateInterface */
        $cacheState = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\Framework\App\Cache\StateInterface'
        );
        foreach ($types as $type) {
            $cacheState->setEnabled($type, false);
        }
        $cacheState->persist();

        /** @var \Magento\Framework\App\Filesystem $filesystem */
        $filesystem = $this->_objectManager->create(
            'Magento\Framework\App\Filesystem',
            array(
                'directoryList' => $this->_objectManager->create(
                    'Magento\Framework\App\Filesystem\DirectoryList',
                    array(
                        'root' => BP,
                        'directories' => array(
                            DirectoryList::MODULES => array('path' => __DIR__ . '/_files'),
                            DirectoryList::CONFIG => array('path' => __DIR__ . '/_files')
                        )
                    )
                )
            )
        );

        /** @var \Magento\Framework\Module\Declaration\FileResolver $modulesDeclarations */
        $modulesDeclarations = $this->_objectManager->create(
            'Magento\Framework\Module\Declaration\FileResolver',
            array('filesystem' => $filesystem)
        );


        /** @var \Magento\Framework\Module\Declaration\Reader\Filesystem $filesystemReader */
        $filesystemReader = $this->_objectManager->create(
            'Magento\Framework\Module\Declaration\Reader\Filesystem',
            array('fileResolver' => $modulesDeclarations)
        );

        /** @var \Magento\Framework\Module\ModuleList $modulesList */
        $modulesList = $this->_objectManager->create(
            'Magento\Framework\Module\ModuleList',
            array('reader' => $filesystemReader)
        );

        /** @var \Magento\Framework\Module\Dir\Reader $moduleReader */
        $moduleReader = $this->_objectManager->create(
            'Magento\Framework\Module\Dir\Reader',
            array('moduleList' => $modulesList, 'filesystem' => $filesystem)
        );
        $moduleReader->setModuleDir('Magento_Test', 'etc', __DIR__ . '/_files/Magento/Test/etc');

        /** @var \Magento\Framework\App\Config\FileResolver $fileResolver */
        $fileResolver = $this->_objectManager->create(
            'Magento\Framework\App\Config\FileResolver',
            array('moduleReader' => $moduleReader)
        );

        /** @var \Magento\Install\Model\Config\Reader $configReader */
        $configReader = $this->_objectManager->create(
            'Magento\Install\Model\Config\Reader',
            array('fileResolver' => $fileResolver)
        );

        $configData = $this->_objectManager->create(
            'Magento\Install\Model\Config\Data',
            array('reader' => $configReader)
        );

        $this->_object = $this->_objectManager->create(
            'Magento\Install\Model\Config',
            array('dataStorage' => $configData)
        );
    }

    public function testGetWizardSteps()
    {
        $steps = $this->_object->getWizardSteps();
        $this->assertEquals(2, count($steps));
        $this->assertCount(2, $steps);
        $this->assertEquals(array('begin', 'locale'), array($steps[0]->getName(), $steps[1]->getName()));
    }

    public function testGetWritableFullPathsForCheck()
    {
        $directories = $this->_object->getWritableFullPathsForCheck();
        $this->assertEquals(2, count($directories));
        $this->assertCount(2, $directories);
        $this->assertEquals('1', $directories['etc']['existence']);
        $this->assertEquals('0', $directories['etc']['recursive']);
        $this->assertTrue(array_key_exists('path', $directories['etc']));
        $this->assertEquals('1', $directories['var']['existence']);
        $this->assertEquals('1', $directories['var']['recursive']);
        $this->assertTrue(array_key_exists('path', $directories['var']));
    }

    public function testGetPathForCheck()
    {
        $directories = $this->_object->getPathForCheck();
        $this->assertEquals(2, count($directories['writeable']));
        $this->assertCount(2, $directories['writeable']);
        $expected = array(
            array('existence' => '1', 'recursive' => '0'),
            array('existence' => '1', 'recursive' => '1')
        );
        $this->assertEquals($expected, $directories['writeable']);
    }

    public function testMergeCompleteAndPartial()
    {
        $fileList = array(
            file_get_contents(__DIR__ . '/_files/install_wizard_complete.xml'),
            file_get_contents(__DIR__ . '/_files/install_wizard_partial.xml')
        );
        $fileResolverMock = $this->getMockBuilder(
            'Magento\Framework\Config\FileResolverInterface'
        )->setMethods(
            array('get')
        )->disableOriginalConstructor()->getMock();
        $fileResolverMock->expects(
            $this->once()
        )->method(
            'get'
        )->with(
            $this->equalTo('install_wizard.xml')
        )->will(
            $this->returnValue($fileList)
        );

        $configReader = $this->_objectManager->create(
            'Magento\Install\Model\Config\Reader',
            array('fileResolver' => $fileResolverMock)
        );

        $configData = $this->_objectManager->create(
            'Magento\Install\Model\Config\Data',
            array('reader' => $configReader)
        );

        /** @var \Magento\Install\Model\Config $model */
        $model = $this->_objectManager->create('Magento\Install\Model\Config', array('dataStorage' => $configData));

        $expectedSteps = array(
            array(
                'name' => "begin",
                'controller' => 'wizard_custom',
                'action' => 'begin',
                'code' => 'License Agreement Updated'
            ),
            array(
                'name' => "after_end",
                'controller' => 'wizard_custom',
                'action' => 'after_end',
                'code' => 'One more thing..'
            )
        );

        $steps = $model->getWizardSteps();

        $counter = 0;
        foreach ($steps as $step) {
            if (isset($expectedSteps[$counter])) {
                $this->assertEquals($expectedSteps[$counter], $step->getData());
                $counter++;
            } else {
                $this->fail('It is more Install steps than expected');
            }
        }
        if (count($expectedSteps) > $counter + 1) {
            $this->fail('Some expected steps are missing');
        }
        $pathsForCheck = $model->getWritableFullPathsForCheck();
        $this->assertArrayHasKey('etc', $pathsForCheck);
        $this->assertArrayHasKey('media', $pathsForCheck);
        $this->assertEquals('1', $pathsForCheck['etc']['recursive']);
    }
}
