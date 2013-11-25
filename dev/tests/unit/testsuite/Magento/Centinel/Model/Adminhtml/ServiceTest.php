<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for \Magento\Centinel\Model\Adminhtml\Service
 */
namespace Magento\Centinel\Model\Adminhtml;

class ServiceTest extends \Magento\Centinel\Model\ServiceTest
{
    /**
     * Create mock for URL model
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function _getUrlMock()
    {
        return $this->getMock('Magento\Backend\Model\Url', array('getUrl'), array(), '', false);
    }

    /**
     * @covers \Magento\Centinel\Model\Adminhtml\Service::_getUrl
     */
    public function testGetUrl()
    {
        $this->_url->expects($this->once())->method('getUrl')->will($this->returnValue('adminhtml/centinel/test'));
        $method = new \ReflectionMethod('Magento\Centinel\Model\Adminhtml\Service', '_getUrl');
        $method->setAccessible(true);
        $this->assertEquals('adminhtml/centinel/test', $method->invoke($this->_model, 'test', array()));
    }
}
