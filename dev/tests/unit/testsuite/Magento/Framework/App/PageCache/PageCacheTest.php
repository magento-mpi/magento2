<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\App\PageCache;

class PageCacheTest extends \PHPUnit_Framework_TestCase
{
    public function testIdentifierProperty()
    {
        $identifier = 'page_cache';

        $poolMock = $this->getMockBuilder('\Magento\Framework\App\Cache\Frontend\Pool')->disableOriginalConstructor()->getMock();
        $poolMock->expects(
            $this->once()
        )->method(
            'get'
        )->with(
            $this->equalTo($identifier)
        )->will(
            $this->returnArgument(0)
        );
        $model = new \Magento\Framework\App\PageCache\Cache($poolMock);
        $this->assertInstanceOf('Magento\Framework\App\PageCache\Cache', $model);
    }
}
