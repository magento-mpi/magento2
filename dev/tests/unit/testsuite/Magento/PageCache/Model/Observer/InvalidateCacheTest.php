<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\PageCache\Model\Observer;

class InvalidateCacheTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\PageCache\Model\Observer\InvalidateCache */
    protected $_model;

    /** @var \PHPUnit_Framework_MockObject_MockObject|\Magento\PageCache\Model\Config */
    protected $_configMock;

    /** @var  \PHPUnit_Framework_MockObject_MockObject|\Magento\Framework\App\Cache\TypeListInterface */
    protected $_typeListMock;

    /**
     * Set up all mocks and data for test
     */
    public function setUp()
    {
        $this->_configMock = $this->getMock(
            'Magento\PageCache\Model\Config',
            array('getType', 'isEnabled'),
            array(),
            '',
            false
        );
        $this->_typeListMock = $this->getMock('Magento\Framework\App\Cache\TypeList', array(), array(), '', false);

        $this->_model = new \Magento\PageCache\Model\Observer\InvalidateCache(
            $this->_configMock,
            $this->_typeListMock
        );
    }

    /**
     * @dataProvider invalidateCacheDataProvider
     * @param bool $cacheState
     */
    public function testExecute($cacheState)
    {
        $this->_configMock->expects($this->once())->method('isEnabled')->will($this->returnValue($cacheState));

        if ($cacheState) {
            $this->_typeListMock->expects($this->once())->method('invalidate')->with($this->equalTo('full_page'));
        }
        $this->_model->execute();
    }

    public function invalidateCacheDataProvider()
    {
        return array(array(true), array(false));
    }

} 
