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

class Mage_Core_Model_Design_FileResolution_Strategy_Fallback_CachingProxyTest extends PHPUnit_Framework_TestCase
{
    /**
     * Mock of the model to be tested. Operates the mocked fallback object.
     *
     * @var Mage_Core_Model_Design_FileResolution_Strategy_Fallback_CachingProxy|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_model;

    /**
     * Mocked fallback object, with file resolution methods ready to be substituted.
     *
     * @var Mage_Core_Model_Design_FileResolution_Strategy_Fallback|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_fallback;

    /**
     * Mocked factory for fallback objects
     *
     * @var Mage_Core_Model_Design_FileResolution_Strategy_Fallback_Factory|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_fallbackFactory;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_map;

    public function setUp()
    {
        $this->_fallback = $this->getMock(
            'Mage_Core_Model_Design_FileResolution_Strategy_Fallback',
            array(),
            array(),
            '',
            false
        );

        if (class_exists('Mage_Core_Model_Design_FileResolution_Strategy_Fallback_Factory')) {
            $className = 'Mage_Core_Model_Design_FileResolution_Strategy_Fallback_Factory';
            $mockClassName = '';
        } else {
            $className = 'stdClass';
            $mockClassName = 'Mage_Core_Model_Design_FileResolution_Strategy_Fallback_Factory';
        }
        $this->_fallbackFactory = $this->getMock($className, array('createFromArray'), array(),
            $mockClassName, false);
        $this->_fallbackFactory->expects($this->any())
            ->method('createFromArray')
            ->will($this->returnValue($this->_fallback));

        $this->_map = $this->getMock('Mage_Core_Model_Design_FileResolution_Strategy_Fallback_CachingProxy_Map',
            array(), array(), '', false);

        $this->_model = new Mage_Core_Model_Design_FileResolution_Strategy_Fallback_CachingProxy(
            $this->_map, $this->_fallbackFactory
        );
    }

    /**
     * @param string $method
     * @param array $params
     * @param string $expectedResult
     * @dataProvider proxyMethodsDataProvider
     * @covers Mage_Core_Model_Design_FileResolution_Strategy_Fallback_CachingProxy::getFile
     * @covers Mage_Core_Model_Design_FileResolution_Strategy_Fallback_CachingProxy::getLocaleFile
     * @covers Mage_Core_Model_Design_FileResolution_Strategy_Fallback_CachingProxy::getViewFile
     */
    public function testProxyMethods($method, $params, $expectedResult)
    {
        $this->_map->expects($this->once())
            ->method('get');
        $this->_map->expects($this->once())
            ->method('set');

        $helper = new Magento_Test_Helper_ProxyTesting();
        $actualResult = $helper->invokeWithExpectations($this->_model, $this->_fallback, $method, $params,
            $expectedResult);
        $this->assertEquals($expectedResult, $actualResult);
    }

    /**
     * @return array
     */
    public static function proxyMethodsDataProvider()
    {
        $themeModel = PHPUnit_Framework_MockObject_Generator::getMock(
            'Mage_Core_Model_Theme',
            array(),
            array(),
            '',
            false,
            false
        );
        return array(
            'getFile' => array(
                'getFile',
                array('area51', $themeModel, 'file.txt', 'Some_Module'),
                'path/to/file.txt',
            ),
            'getLocaleFile' => array(
                'getLocaleFile',
                array('area51', $themeModel, 'sq_AL', 'file.txt'),
                'path/to/locale_file.txt',
            ),
            'getViewFile' => array(
                'getViewFile',
                array('area51', $themeModel, 'uk_UA', 'file.txt', 'Some_Module'),
                'path/to/view_file.txt',
            ),
        );
    }

    public function testSetViewFilePathToMap()
    {
        $materializedPathToFile = '/public/long/path/to/file.txt';

        $themeModel = $this->getMock('Mage_Core_Model_Theme', array(), array(), '', false, false);

        $this->_map->expects($this->at(0))
            ->method('set')
            ->with('view', 'area51', $themeModel, 'en_US', 'Some_Module', 'file.txt', $materializedPathToFile)
            ->will($this->returnValue(null));

        $result = $this->_model->setViewFilePathToMap('area51', $themeModel, 'en_US', 'Some_Module', 'file.txt',
            $materializedPathToFile);
        $this->assertEquals($this->_model, $result);
    }
}
