<?php
/**
 * Test class for \Magento\Store\Model\Store\StoresConfig
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Store\Model;

class StoresConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Store\Model\StoresConfig
     */
    protected $_model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_storeManager;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_storeOne;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_storeTwo;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_config;

    protected function setUp()
    {
        $this->_storeOne = $this->getMock('Magento\Store\Model\Store', array(), array(), '', false);
        $this->_storeTwo = $this->getMock('Magento\Store\Model\Store', array(), array(), '', false);
        $this->_storeManager = $this->getMock('Magento\Store\Model\StoreManagerInterface');
        $this->_config = $this->getMock('Magento\Framework\App\Config\ScopeConfigInterface');

        $this->_model = new \Magento\Store\Model\StoresConfig(
            $this->_storeManager,
            $this->_config
        );
    }

    public function testGetStoresConfigByPath()
    {
        $path = 'config/path';

        $this->_storeOne
            ->expects($this->at(0))
            ->method('getCode')
            ->will($this->returnValue('code_0'));

        $this->_storeOne
            ->expects($this->at(1))
            ->method('getId')
            ->will($this->returnValue(0));

        $this->_storeTwo
            ->expects($this->at(0))
            ->method('getCode')
            ->will($this->returnValue('code_1'));

        $this->_storeTwo
            ->expects($this->at(1))
            ->method('getId')
            ->will($this->returnValue(1));

        $this->_storeManager
            ->expects($this->once())
            ->method('getStores')
            ->with(true)
            ->will($this->returnValue(array(0 => $this->_storeOne, 1 => $this->_storeTwo)));

        $this->_config
            ->expects($this->at(0))
            ->method('getValue')
            ->with($path, 'store', 'code_0')
            ->will($this->returnValue(0));

        $this->_config
            ->expects($this->at(1))
            ->method('getValue')
            ->with($path, 'store', 'code_1')
            ->will($this->returnValue(1));

        $this->assertEquals(array(0 => 0, 1 => 1), $this->_model->getStoresConfigByPath($path));
    }
}
