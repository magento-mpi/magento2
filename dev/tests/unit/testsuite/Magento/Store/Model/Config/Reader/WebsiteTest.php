<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Store\Model\Config\Reader;

class WebsiteTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Store\Model\Config\Reader\Website
     */
    protected $_model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_initialConfigMock;

    /**
     * @var \Magento\Framework\App\Config\ScopePool|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_scopePullMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_collectionFactory;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_websiteMock;

    protected function setUp()
    {
        $this->_initialConfigMock = $this->getMock('Magento\Framework\App\Config\Initial', array(), array(), '', false);
        $this->_scopePullMock = $this->getMock('Magento\Framework\App\Config\ScopePool', array(), array(), '', false);
        $this->_collectionFactory = $this->getMock(
            'Magento\Store\Model\Resource\Config\Collection\ScopedFactory',
            array('create'),
            array(),
            '',
            false
        );
        $websiteFactoryMock = $this->getMock(
            'Magento\Store\Model\WebsiteFactory',
            array('create'),
            array(),
            '',
            false
        );
        $this->_websiteMock = $this->getMock('Magento\Store\Model\Website', array(), array(), '', false);
        $websiteFactoryMock->expects($this->any())->method('create')->will($this->returnValue($this->_websiteMock));

        $this->_model = new \Magento\Store\Model\Config\Reader\Website(
            $this->_initialConfigMock,
            $this->_scopePullMock,
            new \Magento\Framework\App\Config\Scope\Converter(),
            $this->_collectionFactory,
            $websiteFactoryMock
        );
    }

    public function testRead()
    {
        $websiteCode = 'default';
        $websiteId = 1;

        $dataMock = $this->getMock('Magento\Framework\App\Config\Data', array(), array(), '', false);
        $dataMock->expects(
            $this->any()
        )->method(
            'getValue'
        )->will(
            $this->returnValue(array('config' => array('key0' => 'default_value0', 'key1' => 'default_value1')))
        );
        $dataMock->expects(
            $this->once()
        )->method(
            'getSource'
        )->will(
            $this->returnValue(array('config' => array('key0' => 'default_value0', 'key1' => 'default_value1')))
        );
        $this->_scopePullMock->expects(
            $this->once()
        )->method(
            'getScope'
        )->with(
            'default',
            null
        )->will(
            $this->returnValue($dataMock)
        );

        $this->_initialConfigMock->expects(
            $this->any()
        )->method(
            'getData'
        )->with(
            "websites|{$websiteCode}"
        )->will(
            $this->returnValue(array('config' => array('key1' => 'website_value1', 'key2' => 'website_value2')))
        );
        $this->_websiteMock->expects($this->once())->method('load')->with($websiteCode);
        $this->_websiteMock->expects($this->any())->method('getId')->will($this->returnValue($websiteId));
        $this->_collectionFactory->expects(
            $this->once()
        )->method(
            'create'
        )->with(
            array('scope' => 'websites', 'scopeId' => $websiteId)
        )->will(
            $this->returnValue(
                array(
                    new \Magento\Framework\Object(array('path' => 'config/key1', 'value' => 'website_db_value1')),
                    new \Magento\Framework\Object(array('path' => 'config/key3', 'value' => 'website_db_value3'))
                )
            )
        );
        $expectedData = array(
            'config' => array(
                'key0' => 'default_value0',
                'key1' => 'website_db_value1',
                'key2' => 'website_value2',
                'key3' => 'website_db_value3'
            )
        );
        $this->assertEquals($expectedData, $this->_model->read($websiteCode));
    }
}
