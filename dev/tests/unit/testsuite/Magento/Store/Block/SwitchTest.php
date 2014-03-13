<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Store\Block;

class SwitchTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Core\Block\Switcher
     */
    protected $_block;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_storeManagerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_appMock;

    protected function setUp()
    {
        $this->_storeManagerMock = $this->getMock('\Magento\Core\Model\StoreManagerInterface');

        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->_block = $helper->getObject(
            'Magento\Core\Block\Switcher', array('storeManager' => $this->_storeManagerMock)
        );
    }

    /**
     * @dataProvider testIsStoreInUrlDataProvider
     */
    public function testIsStoreInUrl($isUseStoreInUrl)
    {
        $storeMock = $this->getMock('Magento\Core\Model\Store', array(), array(), '', false);

        $storeMock->expects($this->once())->method('isUseStoreInUrl')->will($this->returnValue($isUseStoreInUrl));

        $this->_storeManagerMock->expects($this->any())->method('getStore')->will($this->returnValue($storeMock));
        $this->assertEquals($this->_block->isStoreInUrl(), $isUseStoreInUrl);
        // check value is cached
        $this->assertEquals($this->_block->isStoreInUrl(), $isUseStoreInUrl);
    }

    /**
     * @see self::testIsStoreInUrlDataProvider()
     * @return array
     */
    public function testIsStoreInUrlDataProvider()
    {
        return array(
            array(true),
            array(false),
        );
    }
}
