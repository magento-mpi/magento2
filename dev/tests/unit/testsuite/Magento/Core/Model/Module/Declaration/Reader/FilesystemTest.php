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
     * @var \Magento\Core\Model\Module\Declaration\Reader\Filesystem
     */
    protected $_model;

    protected function setUp()
    {
        $baseDir = __DIR__ . str_replace('/', DIRECTORY_SEPARATOR, '/../FileResolver/_files');
        $applicationDirs = $this->getMock('Magento\Core\Model\Dir', array(), array('getDir'), '', false);
        $applicationDirs->expects($this->any())->method('getDir')
            ->will($this->returnValueMap(array(
                array(
                    \Magento\Core\Model\Dir::CONFIG, $baseDir . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR .'etc',
                ),
                array(
                    \Magento\Core\Model\Dir::MODULES,
                        $baseDir . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR .'code',
                ),
            )));
        $fileResolver = new \Magento\Core\Model\Module\Declaration\FileResolver($applicationDirs);
        $converter = new \Magento\Core\Model\Module\Declaration\Converter\Dom();
        $schemaLocatorMock = $this->getMock(
            '\Magento\Core\Model\Module\Declaration\SchemaLocator', array(), array(), '', false
        );
        $validationStateMock = $this->getMock('Magento\Config\ValidationStateInterface');
        $this->_model = new \Magento\Core\Model\Module\Declaration\Reader\Filesystem(
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
