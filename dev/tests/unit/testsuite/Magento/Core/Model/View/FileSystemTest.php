<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test for view filesystem model
 */
class Magento_Core_Model_View_FileSystemTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Core_Model_View_FileSystem|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_model;

    /**
     * @var Magento_Core_Model_Design_FileResolution_StrategyPool|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_strategyPool;

    /**
     * @var Magento_Core_Model_View_Service|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_viewService;


    protected function setUp()
    {
        $this->_strategyPool = $this->getMock('Magento_Core_Model_Design_FileResolution_StrategyPool', array(),
            array(), '', false
        );
        $this->_viewService = $this->getMock('Magento_Core_Model_View_Service',
            array('extractScope', 'updateDesignParams'), array(), '', false
        );

        $this->_model = new Magento_Core_Model_View_FileSystem($this->_strategyPool, $this->_viewService);
    }

    public function testGetFilename()
    {
        $params = array(
            'area'       => 'some_area',
            'themeModel' => $this->getMock('Magento_Core_Model_Theme', array(), array(), '', false, false),
            'module'     => 'Some_Module'   //It should be set in Magento_Core_Model_View_Service::extractScope
                                            // but PHPUnit has problems with passing arguments by reference
        );
        $file = 'Some_Module::some_file.ext';
        $expected = 'path/to/some_file.ext';

        $strategyMock = $this->getMock('Magento_Core_Model_Design_FileResolution_Strategy_FileInterface');
        $strategyMock->expects($this->once())
            ->method('getFile')
            ->with($params['area'], $params['themeModel'], 'some_file.ext', 'Some_Module')
            ->will($this->returnValue($expected));

        $this->_strategyPool->expects($this->once())
            ->method('getFileStrategy')
            ->with(false)
            ->will($this->returnValue($strategyMock));

        $this->_viewService->expects($this->any())
            ->method('extractScope')
            ->with($file, $params)
            ->will($this->returnValue('some_file.ext'));

        $actual = $this->_model->getFilename($file, $params);
        $this->assertEquals($expected, $actual);
    }

    public function testGetLocaleFileName()
    {
        $params = array(
            'area' => 'some_area',
            'themeModel' => $this->getMock('Magento_Core_Model_Theme', array(), array(), '', false, false),
            'locale' => 'some_locale'
        );
        $file = 'some_file.ext';
        $expected = 'path/to/some_file.ext';

        $strategyMock = $this->getMock('Magento_Core_Model_Design_FileResolution_Strategy_LocaleInterface');
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
            'area'       => 'some_area',
            'themeModel' => $this->getMock('Magento_Core_Model_Theme', array(), array(), '', false, false),
            'locale'     => 'some_locale',
            'module'     => 'Some_Module'   //It should be set in Magento_Core_Model_View_Service::extractScope
                                            // but PHPUnit has problems with passing arguments by reference
        );
        $file = 'Some_Module::some_file.ext';
        $expected = 'path/to/some_file.ext';

        $strategyMock = $this->getMock('Magento_Core_Model_Design_FileResolution_Strategy_ViewInterface');
        $strategyMock->expects($this->once())
            ->method('getViewFile')
            ->with($params['area'], $params['themeModel'], $params['locale'], 'some_file.ext', 'Some_Module')
            ->will($this->returnValue($expected));

        $this->_strategyPool->expects($this->once())
            ->method('getViewStrategy')
            ->with(false)
            ->will($this->returnValue($strategyMock));

        $this->_viewService->expects($this->any())
            ->method('extractScope')
            ->with($file, $params)
            ->will($this->returnValue('some_file.ext'));

        $actual = $this->_model->getViewFile($file, $params);
        $this->assertEquals($expected, $actual);
    }
}
