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
namespace Magento\Core\Model\View\Url;

class ConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Core\Model\View\Url\Config
     */
    protected $_model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\App\Config\ScopeConfigInterface
     */
    protected $_storeConfig;

    protected function setUp()
    {
        $this->_storeConfig = $this->getMockBuilder('Magento\App\Config\ScopeConfigInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_model = new \Magento\Core\Model\View\Url\Config($this->_storeConfig);
    }

    /**
     * @param $path
     * @param $expectedValue
     *
     * @dataProvider getConfigDataProvider
     */
    public function testGetValue($path, $expectedValue)
    {
        $this->_storeConfig
            ->expects($this->any())
            ->method('getValue')
            ->with($path)
            ->will($this->returnValue($expectedValue));
        $actual = $this->_model->getValue($path);
        $this->assertEquals($expectedValue, $actual);
    }

    /**
     * @return array
     */
    public function getConfigDataProvider()
    {
        return array(
            array('some/valid/path1', 'someValue'),
            array('some/valid/path2', 2),
            array('some/valid/path3', false),
            array('some/invalid/path3', null)
        );
    }
}
