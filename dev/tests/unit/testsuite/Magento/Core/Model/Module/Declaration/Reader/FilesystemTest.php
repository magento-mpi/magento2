<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Core_Model_Module_Declaration_Reader_FilesystemTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Core_Model_Module_Declaration_Reader_Filesystem
     */
    protected $_model;

    protected function setUp()
    {
        $baseDir = __DIR__ . str_replace('/', DIRECTORY_SEPARATOR, '/../FileResolver/_files');
        $applicationDirs = $this->getMock('Magento_Core_Model_Dir', array(), array('getDir'), '', false);
        $applicationDirs->expects($this->any())->method('getDir')
            ->will($this->returnValueMap(array(
                array(
                    Magento_Core_Model_Dir::CONFIG, $baseDir . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR .'etc',
                ),
                array(
                    Magento_Core_Model_Dir::MODULES,
                        $baseDir . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR .'code',
                ),
            )));
        $fileResolver = new Mage_Core_Model_Module_Declaration_FileResolver($applicationDirs);
        $converter = new Mage_Core_Model_Module_Declaration_Converter_Dom();
        $schemaLocatorMock = $this->getMock(
            'Mage_Core_Model_Module_Declaration_SchemaLocator', array(), array(), '', false
        );
        $validationStateMock = $this->getMock('Magento_Config_ValidationStateInterface');
        $this->_model = new Mage_Core_Model_Module_Declaration_Reader_Filesystem(
            $fileResolver, $converter, $schemaLocatorMock, $validationStateMock
        );
    }

    public function testRead()
    {
        $expectedResult = array(
            'Module_One' => array(
                'name' => 'Module_One',
                'version' => '1.0.0.0',
                'active' => true,
                'dependencies' => array(
                    'modules' => array(),
                    'extensions' => array(
                        'strict' => array(
                            array('name' => 'simplexml'),
                        ),
                        'alternatives' => array(array(
                            array('name' => 'gd'),
                            array('name' => 'imagick', 'minVersion' => '3.0.0'),
                        )),
                    ),
                ),
            ),
            'Module_Four' => array(
                'name' => 'Module_Four',
                'version' => '1.0.0.0',
                'active' => true,
                'dependencies' => array(
                    'modules' => array('Module_One'),
                    'extensions' => array(
                        'strict' => array(),
                        'alternatives' => array(),
                    ),
                ),
            ),
            'Module_Three' => array(
                'name' => 'Module_Three',
                'version' => '1.0.0.0',
                'active' => true,
                'dependencies' => array(
                    'modules' => array('Module_Four'),
                    'extensions' => array(
                        'strict' => array(),
                        'alternatives' => array(),
                    ),
                ),
            ),
        );
        $this->assertEquals($expectedResult, $this->_model->read('global'));
    }
}
