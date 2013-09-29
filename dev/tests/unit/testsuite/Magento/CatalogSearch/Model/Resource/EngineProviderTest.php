<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CatalogSearch
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogSearch\Model\Resource;

class EngineProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var $_model \Magento\CatalogSearch\Model\Resource\EngineProvider
     */
    protected $_model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\CatalogSearch\Model\Resource\EngineFactory
     */
    protected $_engineFactoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Core\Model\Store\Config
     */
    protected $_storeConfigMock;

    protected function setUp()
    {
        $this->_engineFactoryMock = $this->getMock('Magento\CatalogSearch\Model\Resource\EngineFactory',
            array('create'), array(), '', false);
        $this->_storeConfigMock = $this->getMock('Magento\Core\Model\Store\Config',
            array('getConfig'), array(), '', false);

        $this->_model = new \Magento\CatalogSearch\Model\Resource\EngineProvider($this->_engineFactoryMock,
            $this->_storeConfigMock);
    }

    public function testGetPositive()
    {
        $engineMock = $this->getMock('Magento\CatalogSearch\Model\Resource\Fulltext\Engine',
            array('test'), array(), '', false);
        $engineMock->expects($this->once())
            ->method('test')
            ->will($this->returnValue(true));

        $this->_storeConfigMock->expects($this->once())
            ->method('getConfig')
            ->with('catalog/search/engine')
            ->will($this->returnValue('Magento\CatalogSearch\Model\Resource\Fulltext\Engine'));

        $this->_engineFactoryMock->expects($this->once())
            ->method('create')
            ->with('Magento\CatalogSearch\Model\Resource\Fulltext\Engine')
            ->will($this->returnValue($engineMock));

        $this->assertEquals($engineMock, $this->_model->get());
    }

    public function testGetNegative()
    {
        $engineMock = $this->getMock('Magento\CatalogSearch\Model\Resource\Fulltext\Engine',
            array('test'), array(), '', false);
        $engineMock->expects($this->never())
            ->method('test');

        $this->_storeConfigMock->expects($this->once())
            ->method('getConfig')
            ->with('catalog/search/engine')
            ->will($this->returnValue(''));

        $this->_engineFactoryMock->expects($this->once())
            ->method('create')
            ->with('Magento\CatalogSearch\Model\Resource\Fulltext\Engine')
            ->will($this->returnValue($engineMock));

        $this->assertEquals($engineMock, $this->_model->get());
    }
}
