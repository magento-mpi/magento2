<?php
/**
 * Magento_Install_Model_Config
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Install_Model_ConfigTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Install_Model_Config
     */
    private $_object;

    /**
     * @var Magento_ObjectManager
     */
    private $_objectManager;

    public function setUp()
    {
        $this->_objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
        /** @var $cacheTypeList Magento_Core_Model_Cache_TypeListInterface */
        $cacheTypeList = Mage::getModel('Magento_Core_Model_Cache_TypeListInterface');
        $types = array_keys($cacheTypeList->getTypes());

        /** @var $cacheState Magento_Core_Model_Cache_StateInterface */
        $cacheState = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->get('Magento_Core_Model_Cache_StateInterface');
        foreach ($types as $type) {
            $cacheState->setEnabled($type, false);
        }
        $cacheState->persist();

        /** @var Magento_Core_Model_Dir $dirs */
        $dirs = $this->_objectManager->create(
            'Magento_Core_Model_Dir', array(
                'baseDir' => BP,
                'dirs' => array(
                    Magento_Core_Model_Dir::MODULES => __DIR__ . '/_files',
                    Magento_Core_Model_Dir::CONFIG => __DIR__ . '/_files'
                )
            )
        );

        /** @var Magento_Core_Model_Module_Declaration_FileResolver $modulesDeclarations */
        $modulesDeclarations = $this->_objectManager->create(
            'Magento_Core_Model_Module_Declaration_FileResolver', array(
                'applicationDirs' => $dirs,
            )
        );


        /** @var Magento_Core_Model_Module_Declaration_Reader_Filesystem $filesystemReader */
        $filesystemReader = $this->_objectManager->create(
            'Magento_Core_Model_Module_Declaration_Reader_Filesystem', array(
                'fileResolver' => $modulesDeclarations,
            )
        );

        /** @var Magento_Core_Model_ModuleList $modulesList */
        $modulesList = $this->_objectManager->create(
            'Magento_Core_Model_ModuleList', array(
                'reader' => $filesystemReader,
            )
        );

        /** @var Magento_Core_Model_Config_Modules_Reader $moduleReader */
        $moduleReader = $this->_objectManager->create(
            'Magento_Core_Model_Config_Modules_Reader', array(
                'moduleList' => $modulesList
            )
        );
        $moduleReader->setModuleDir('Magento_Test', 'etc', __DIR__ . '/_files/Magento/Test/etc');

        /** @var Magento_Core_Model_Config_FileResolver $fileResolver */
        $fileResolver = $this->_objectManager->create(
            'Magento_Core_Model_Config_FileResolver', array(
                'moduleReader' => $moduleReader,
            )
        );

        /** @var Magento_Logging_Model_Config_Reader $configReader */
        $configReader = $this->_objectManager->create(
            'Magento_Install_Model_Config_Reader', array(
                'fileResolver' => $fileResolver,
            )
        );

        $configData =  $this->_objectManager->create(
            'Magento_Install_Model_Config_Data', array(
                'reader' => $configReader,
            )
        );

        $this->_object =  $this->_objectManager->create(
            'Magento_Install_Model_Config', array(
                'dataStorage' => $configData,
            )
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
            array(
                'existence' => '1',
                'recursive' => '0'
            ),
            array(
                'existence' => '1',
                'recursive' => '1'
            ),
        );
        $this->assertEquals($expected, $directories['writeable']);

    }

    public function testMergeCompleteAndPartial()
    {
        $fileList = array(
            __DIR__ . '/_files/install_wizard_complete.xml',
            __DIR__ . '/_files/install_wizard_partial.xml'
        );
        $fileResolverMock = $this->getMockBuilder('Magento_Config_FileResolverInterface')
            ->setMethods(array('get'))
            ->disableOriginalConstructor()
            ->getMock();
        $fileResolverMock->expects($this->once())
            ->method('get')
            ->with($this->equalTo('install_wizard.xml'))
            ->will($this->returnValue($fileList));

        $configReader = $this->_objectManager->create(
            'Magento_Install_Model_Config_Reader', array(
                'fileResolver' => $fileResolverMock,
            )
        );

        $configData =  $this->_objectManager->create(
            'Magento_Install_Model_Config_Data', array(
                'reader' => $configReader,
            )
        );

        /** @var Magento_Install_Model_Config $model */
        $model = $this->_objectManager->create(
            'Magento_Install_Model_Config', array(
                'dataStorage' => $configData,
            )
        );

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
        if (count($expectedSteps) > $counter+1) {
            $this->fail('Some expected steps are missing');
        }
        $pathesForCheck = $model->getWritableFullPathsForCheck();
        $this->assertArrayHasKey('etc', $pathesForCheck);
        $this->assertArrayHasKey('media', $pathesForCheck);
        $this->assertArrayHasKey('lib', $pathesForCheck);
        $this->assertEquals('1', $pathesForCheck['etc']['recursive']);
    }
}
