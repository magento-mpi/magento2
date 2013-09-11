<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Core_Model_ObjectManager_ConfigLoaderTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Core\Model\ObjectManager\ConfigLoader
     */
    protected $_model;

    /**
     * @var \Magento\ObjectManager\Config\Reader\Dom
     */
    protected $_readerMock;

    /**
     * @var \Magento\Core\Model\Cache\Type\Config
     */
    protected $_cacheMock;

    protected function setUp()
    {
        $this->_readerMock = $this->getMock('Magento\ObjectManager\Config\Reader\Dom',
            array(), array(), '', false
        );

        $this->_cacheMock = $this->getMock('Magento\Core\Model\Cache\Type\Config', array(), array(), '', false);
        $this->_model = new \Magento\Core\Model\ObjectManager\ConfigLoader($this->_cacheMock, $this->_readerMock);
    }

    /**
     * @param $area
     * @dataProvider loadDataProvider
     */
    public function testLoad($area)
    {
        $configData = array('some' => 'config', 'data' => 'value');

        $this->_cacheMock
            ->expects($this->once())
            ->method('get')
            ->with($area, 'DiConfig')
            ->will($this->returnValue(false));

        $this->_readerMock
            ->expects($this->once())
            ->method('read')
            ->with($area)
            ->will($this->returnValue($configData));

        $this->assertEquals($configData, $this->_model->load($area));
    }

    /**
     * Data provider
     *
     * @return array
     */
    public function loadDataProvider()
    {
        return array(
            'global files' => array('global'),
            'adminhtml files' => array('adminhtml'),
            'any area files' => array('any'),
        );
    }
}
