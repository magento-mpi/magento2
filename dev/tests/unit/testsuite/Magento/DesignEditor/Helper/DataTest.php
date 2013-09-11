<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_DesignEditor
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_DesignEditor_Helper_DataTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test front name prefix
     */
    const TEST_FRONT_NAME = 'test_front_name';

    /**
     * Test disabled cache types
     */
    const TEST_DISABLED_CACHE_TYPES = '<type1 /><type2 />';

    /**
     * @var array
     */
    protected $_disabledCacheTypes = array('type1', 'type2');

    /**
     * @var \Magento\DesignEditor\Helper\Data
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_translatorMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_context;

    protected function setUp()
    {
        $this->_translatorMock = $this->getMock('Magento\Core\Model\Translate', array(), array(), '', false);
        $this->_context = $this->getMock('Magento\Core\Helper\Context', array(), array(), '', false);
        $this->_context
            ->expects($this->any())->method('getTranslator')->will($this->returnValue($this->_translatorMock));
    }

    protected function tearDown()
    {
        unset($this->_model);
        unset($this->_context);
    }

    public function testGetFrontName()
    {
        $this->_model = new \Magento\DesignEditor\Helper\Data($this->_context, $this->_getConfigFrontNameMock());
        $this->assertEquals(self::TEST_FRONT_NAME, $this->_model->getFrontName());
    }

    /**
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    protected function _getConfigFrontNameMock()
    {
        $frontNameNode = new \Magento\Core\Model\Config\Element('<test>' . self::TEST_FRONT_NAME . '</test>');
        $configurationMock = $this->getMock('Magento\Core\Model\Config', array('getNode'), array(), '', false);
        $configurationMock->expects($this->any())
            ->method('getNode')
            ->with(\Magento\DesignEditor\Helper\Data::XML_PATH_FRONT_NAME)
            ->will($this->returnValue($frontNameNode));
        return $configurationMock;
    }

    /**
     * @param string $path
     * @param bool $expected
     * @dataProvider isVdeRequestDataProvider
     */
    public function testIsVdeRequest($path, $expected)
    {
        $this->_model = new \Magento\DesignEditor\Helper\Data($this->_context, $this->_getConfigFrontNameMock());

        $requestMock = $this->getMock('Magento\Core\Controller\Request\Http', array('getOriginalPathInfo'),
            array(), '', false);
        $requestMock->expects($this->once())
            ->method('getOriginalPathInfo')
            ->will($this->returnValue($path));
        $this->assertEquals($expected, $this->_model->isVdeRequest($requestMock));
    }

    /**
     * @return array
     */
    public function isVdeRequestDataProvider()
    {
        $vdePath = self::TEST_FRONT_NAME . '/' . \Magento\DesignEditor\Model\State::MODE_NAVIGATION . '/';
        return array(
            array($vdePath . '1/category.html', true),
            array('/1/category.html', false),
            array('/1/2/3/4/5/6/7/category.html', false)
        );
    }

    public function testGetDisabledCacheTypes()
    {
        $cacheTypesNode = new \Magento\Core\Model\Config\Element('<test>' . self::TEST_DISABLED_CACHE_TYPES . '</test>');

        $configurationMock = $this->getMock('Magento\Core\Model\Config', array('getNode'), array(), '', false);
        $configurationMock->expects($this->once())
            ->method('getNode')
            ->with(\Magento\DesignEditor\Helper\Data::XML_PATH_DISABLED_CACHE_TYPES)
            ->will($this->returnValue($cacheTypesNode));

        $this->_model = new \Magento\DesignEditor\Helper\Data($this->_context, $configurationMock);
        $this->assertEquals($this->_disabledCacheTypes, $this->_model->getDisabledCacheTypes());
    }

    public function testGetAvailableModes()
    {
        $configurationMock = $this->getMock('Magento\Core\Model\Config', array('getNode'), array(), '', false);
        $this->_model = new \Magento\DesignEditor\Helper\Data($this->_context, $configurationMock);
        $this->assertEquals(array(\Magento\DesignEditor\Model\State::MODE_NAVIGATION),
            $this->_model->getAvailableModes());
    }
}
