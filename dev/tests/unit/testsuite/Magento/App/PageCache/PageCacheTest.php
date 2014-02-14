<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\App\PageCache;

class PageCacheTest extends \PHPUnit_Framework_TestCase
{
    public function testIdentifierProperty()
    {
        $identifier = 'page_cache';

        $poolMock = $this->getMockBuilder('\Magento\App\Cache\Frontend\Pool')
            ->disableOriginalConstructor()
            ->getMock();
        $poolMock->expects($this->once())
            ->method('get')
            ->with($this->equalTo($identifier))
            ->will($this->returnArgument(0));
        $model = new \Magento\App\PageCache\Cache($poolMock);
        $this->assertInstanceOf('Magento\App\PageCache\Cache', $model);
    }
}
