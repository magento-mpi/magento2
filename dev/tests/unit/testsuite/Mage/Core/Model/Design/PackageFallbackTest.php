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
     * @var Mage_Core_Model_Design_PackageInterface|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_model;

    /**
     * @var Mage_Core_Model_Design_FileResolution_StrategyPool|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_strategyPool;

    protected function setUp()
    {
        $modulesReader = $this->getMock('Mage_Core_Model_Config_Modules_Reader', array(), array(), '', array());
        $filesystem = $this->getMock('Magento_Filesystem', array(), array(), '', array());
        $this->_strategyPool = $this->getMock('Mage_Core_Model_Design_FileResolution_StrategyPool', array(),
            array(), '', false);
        $appState = new Mage_Core_Model_App_State();
        $storeManager = $this->getMock('Mage_Core_Model_StoreManagerInterface');
        $configWriter = $this->getMock('Mage_Core_Model_Config_Storage_WriterInterface');

        $this->_model = $this->getMock('Mage_Core_Model_Design_Package', array('_updateParamDefaults'),
            array($modulesReader, $filesystem, $this->_strategyPool, $appState, $storeManager, $configWriter)
        );
    }

    public function testGetFilename()
    {
        $params = array(
            'area' => 'some_area',
            'themeModel' => $this->getMock('Mage_Core_Model_Theme', array(), array(), '', false, false),
        );
        $file = 'Some_Module::some_file.ext';
        $expected = 'path/to/some_file.ext';

        $strategyMock = $this->getMock('Mage_Core_Model_Design_FileResolution_Strategy_FileInterface');
        $strategyMock->expects($this->once())
            ->method('getFile')
            ->with($params['area'], $params['themeModel'], 'some_file.ext', 'Some_Module')
            ->will($this->returnValue($expected));

        $this->_strategyPool->expects($this->once())
            ->method('getFileStrategy')
            ->with(false)
            ->will($this->returnValue($strategyMock));

        $actual = $this->_model->getFilename($file, $params);
        $this->assertEquals($expected, $actual);
    }

    public function testGetLocaleFileName()
    {
        $params = array(
            'area' => 'some_area',
            'themeModel' => $this->getMock('Mage_Core_Model_Theme', array(), array(), '', false, false),
            'locale' => 'some_locale'
        );
        $file = 'some_file.ext';
        $expected = 'path/to/some_file.ext';

        $strategyMock = $this->getMock('Mage_Core_Model_Design_FileResolution_Strategy_LocaleInterface');
        $strategyMock->expects($this->once())
            ->method('getLocaleFile')
            ->with($params['area'], $params['themeModel'], $params['locale'], 'some_file.ext')
            ->will($this->returnValue($expected));

        $this->_strategyPool->expects($this->once())
            ->method('getLocaleStrategy')
            ->with(false)
            ->will($this->returnValue($strategyMock));

        $actual = $this->_model->getLocaleFileName($file, $params);
        $this->assertEquals($expected, $actual);
    }

    public function testGetViewFile()
    {
        $params = array(
            'area' => 'some_area',
            'themeModel' => $this->getMock('Mage_Core_Model_Theme', array(), array(), '', false, false),
            'locale' => 'some_locale'
        );
        $file = 'Some_Module::some_file.ext';
        $expected = 'path/to/some_file.ext';

        $strategyMock = $this->getMock('Mage_Core_Model_Design_FileResolution_Strategy_ViewInterface');
        $strategyMock->expects($this->once())
            ->method('getViewFile')
            ->with($params['area'], $params['themeModel'], $params['locale'], 'some_file.ext', 'Some_Module')
            ->will($this->returnValue($expected));

        $this->_strategyPool->expects($this->once())
            ->method('getViewStrategy')
            ->with(false)
            ->will($this->returnValue($strategyMock));

        $actual = $this->_model->getViewFile($file, $params);
        $this->assertEquals($expected, $actual);
    }
}
