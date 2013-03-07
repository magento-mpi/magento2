<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test that Design Package delegates fallback resolution to a Fallback model
 */
class Mage_Core_Model_Design_PackageFallbackTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Core_Model_Design_Package|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_model;

    /**
     * @var Mage_Core_Model_Design_FileResolution_StrategyPool|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_resolutionModel;

    protected function setUp()
    {
        $modulesReader = $this->getMock('Mage_Core_Model_Config_Modules_Reader', array(), array(), '', array());
        $filesystem = $this->getMock('Magento_Filesystem', array(), array(), '', array());
        $this->_resolutionModel = $this->getMock('Mage_Core_Model_Design_FileResolution_StrategyPool', array(), array(), '', array());

        $this->_model = $this->getMock('Mage_Core_Model_Design_Package', array('_updateParamDefaults'),
            array($modulesReader, $filesystem, $this->_resolutionModel)
        );
    }

    public function testGetFilename()
    {
        $params = array(
            'area' => 'some_area',
            'package' => 'some_package',
            'theme' => 'some_theme',
        );
        $file = 'Some_Module::some_file.ext';
        $expectedParams = $params + array('module' => 'Some_Module');
        $expected = 'path/to/some_file.ext';

        $this->_resolutionModel->expects($this->once())
            ->method('getFile')
            ->with('some_file.ext', $expectedParams)
            ->will($this->returnValue($expected));

        $actual = $this->_model->getFilename($file, $params);
        $this->assertEquals($expected, $actual);
    }

    public function testGetLocaleFileName()
    {
        $params = array(
            'area' => 'some_area',
            'package' => 'some_package',
            'theme' => 'some_theme',
            'locale' => 'some_locale'
        );
        $file = 'some_file.ext';
        $expected = 'path/to/some_file.ext';

        $this->_resolutionModel->expects($this->once())
            ->method('getLocaleFile')
            ->with('some_file.ext')
            ->will($this->returnValue($expected));

        $actual = $this->_model->getLocaleFileName($file, $params);
        $this->assertEquals($expected, $actual);
    }

    public function testGetViewFile()
    {
        $params = array(
            'area' => 'some_area',
            'package' => 'some_package',
            'theme' => 'some_theme',
            'locale' => 'some_locale'
        );
        $file = 'Some_Module::some_file.ext';
        $expectedParams = $params + array('module' => 'Some_Module');
        $expected = 'path/to/some_file.ext';

        $this->_resolutionModel->expects($this->once())
            ->method('getViewFile')
            ->with('some_file.ext', $expectedParams)
            ->will($this->returnValue($expected));

        $actual = $this->_model->getViewFile($file, $params);
        $this->assertEquals($expected, $actual);
    }
}
