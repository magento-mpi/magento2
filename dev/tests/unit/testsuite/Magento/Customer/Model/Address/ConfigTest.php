<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Model\Address;

class ConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_readerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_cacheMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_storeManagerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_addressHelperMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_storeMock;

    /**
     * @var \Magento\Customer\Model\Address\Config
     */
    protected $_model;

    /**
     * @var string
     */
    protected $_cacheId = 'cache_id';

    public function setUp()
    {
        $this->_storeMock = $this->getMock('Magento\Store\Model\Store', array(), array(), '', false);

        $this->_readerMock = $this->getMock(
            'Magento\Customer\Model\Address\Config\Reader',
            array(), array(), '', false
        );
        $this->_cacheMock = $this->getMock('Magento\Config\CacheInterface');
        $this->_storeManagerMock = $this->getMock('Magento\Store\Model\StoreManager', array(), array(), '', false);
        $this->_storeManagerMock
            ->expects($this->once())
            ->method('getStore')
            ->will($this->returnValue($this->_storeMock));

        $this->_addressHelperMock = $this->getMock('Magento\Customer\Helper\Address', array(), array(), '', false);

        $this->_cacheMock
            ->expects($this->once())
            ->method('load')
            ->with($this->_cacheId)
            ->will($this->returnValue(false));

        $fixtureConfigData = require __DIR__ . '/Config/_files/formats_merged.php';

        $this->_readerMock
            ->expects($this->once())
            ->method('read')
            ->will($this->returnValue($fixtureConfigData));

        $this->_cacheMock
            ->expects($this->once())
            ->method('save')
            ->with(serialize($fixtureConfigData), $this->_cacheId);


        $this->_model = new \Magento\Customer\Model\Address\Config(
            $this->_readerMock,
            $this->_cacheMock,
            $this->_storeManagerMock,
            $this->_addressHelperMock,
            $this->_cacheId
        );
    }

    public function testGetStore()
    {
        $this->assertEquals($this->_storeMock, $this->_model->getStore());
    }

    public function testSetStore()
    {
        $this->_model->setStore($this->_storeMock);

        //no call to $_storeManagerMock's method
        $this->assertEquals($this->_storeMock, $this->_model->getStore());
    }

    public function testGetFormats()
    {
        $this->_storeMock->expects($this->once())
            ->method('getId');

        $this->_storeMock->expects($this->any())
            ->method('getConfig')
            ->will($this->returnValue('someValue'));



        $rendererMock = $this->getMock('Magento\Object');

        $this->_addressHelperMock
            ->expects($this->any())
            ->method('getRenderer')
            ->will($this->returnValue($rendererMock));

        $firstExpected = new \Magento\Object();
        $firstExpected->setCode('format_one')
            ->setTitle('format_one_title')
            ->setDefaultFormat('someValue')
            ->setEscapeHtml(false)
            ->setRenderer(null);

        $secondExpected = new \Magento\Object();
        $secondExpected->setCode('format_two')
            ->setTitle('format_two_title')
            ->setDefaultFormat('someValue')
            ->setEscapeHtml(true)
            ->setRenderer(null);
        $expectedResult = array($firstExpected, $secondExpected);

        $this->assertEquals($expectedResult, $this->_model->getFormats());
    }
}
