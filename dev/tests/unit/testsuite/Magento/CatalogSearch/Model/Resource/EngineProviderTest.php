<?php
/**
 * {license_notice}
 *
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
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfigMock;

    protected function setUp()
    {
        $this->_engineFactoryMock = $this->getMock(
            'Magento\CatalogSearch\Model\Resource\EngineFactory',
            array('create'),
            array(),
            '',
            false
        );
        $this->_scopeConfigMock = $this->getMock('Magento\Framework\App\Config\ScopeConfigInterface');

        $this->_model = new \Magento\CatalogSearch\Model\Resource\EngineProvider(
            $this->_engineFactoryMock,
            $this->_scopeConfigMock
        );
    }

    public function testGetPositive()
    {
        $engineMock = $this->getMock(
            'Magento\CatalogSearch\Model\Resource\Fulltext\Engine',
            array('test', '__wakeup'),
            array(),
            '',
            false
        );
        $engineMock->expects($this->once())->method('test')->will($this->returnValue(true));

        $this->_scopeConfigMock->expects(
            $this->once()
        )->method(
            'getValue'
        )->with(
            'catalog/search/engine'
        )->will(
            $this->returnValue('Magento\CatalogSearch\Model\Resource\Fulltext\Engine')
        );

        $this->_engineFactoryMock->expects(
            $this->once()
        )->method(
            'create'
        )->with(
            'Magento\CatalogSearch\Model\Resource\Fulltext\Engine'
        )->will(
            $this->returnValue($engineMock)
        );

        $this->assertEquals($engineMock, $this->_model->get());
    }
}
