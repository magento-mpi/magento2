<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Model_Config_FileResolverTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Core_Model_Config_FileResolver
     */
    protected $_model;

    protected function setUp()
    {
        $appConfigDir = __DIR__ . DIRECTORY_SEPARATOR . 'FileResolver'
            . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'etc';

        $applicationDirs = $this->getMock('Mage_Core_Model_Dir', array(), array('getDir'), '', false);
        $applicationDirs->expects($this->any())
            ->method('getDir')
            ->with(Mage_Core_Model_Dir::CONFIG)
            ->will($this->returnValue($appConfigDir));

        $moduleReader = $this->getMock('Mage_Core_Model_Config_Modules_Reader', array(),
            array('getModuleConfigurationFiles'), '', false);
        $moduleReader->expects($this->any())
            ->method('getModuleConfigurationFiles')
            ->will($this->returnValueMap(
                array(
                    array(
                        'adminhtml' . DIRECTORY_SEPARATOR . 'di.xml',
                        array(
                            'app/code/Custom/FirstModule/adminhtml/di.xml',
                            'app/code/Custom/SecondModule/adminhtml/di.xml',
                        )
                    ),
                    array(
                        'frontend' . DIRECTORY_SEPARATOR . 'di.xml',
                        array(
                            'app/code/Custom/FirstModule/frontend/di.xml',
                            'app/code/Custom/SecondModule/frontend/di.xml',
                        )
                    ),
                    array(
                        'di.xml',
                        array(
                            'app/code/Custom/FirstModule/di.xml',
                            'app/code/Custom/SecondModule/di.xml',
                        )
                    ),
                )
            ));
        $this->_model = new Mage_Core_Model_Config_FileResolver($moduleReader, $applicationDirs);
    }

    /**
     * @param array $expectedResult
     * @param string $scope
     * @param string $filename
     * @dataProvider getMethodDataProvider
     */
    public function testGet(array $expectedResult, $scope, $filename)
    {
        $this->assertEquals($expectedResult, $this->_model->get($filename, $scope));
    }

    /**
     * @return array
     */
    public function getMethodDataProvider()
    {
        $appConfigDir = __DIR__ . DIRECTORY_SEPARATOR . 'FileResolver'
            . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'etc';
        return array(
            array(
                array(
                    $appConfigDir . DIRECTORY_SEPARATOR . 'config.xml',
                    $appConfigDir . DIRECTORY_SEPARATOR . 'custom' . DIRECTORY_SEPARATOR . 'config.xml',
                ),
                'primary',
                'config.xml',
            ),
            array(
                array(
                    'app/code/Custom/FirstModule/di.xml',
                    'app/code/Custom/SecondModule/di.xml',
                ),
                'global',
                'di.xml',
            ),
            array(
                array(
                    'app/code/Custom/FirstModule/frontend/di.xml',
                    'app/code/Custom/SecondModule/frontend/di.xml',
                ),
                'frontend',
                'di.xml',
            ),
            array(
                array(
                    'app/code/Custom/FirstModule/adminhtml/di.xml',
                    'app/code/Custom/SecondModule/adminhtml/di.xml',
                ),
                'adminhtml',
                'di.xml',
            ),
        );
    }
}
