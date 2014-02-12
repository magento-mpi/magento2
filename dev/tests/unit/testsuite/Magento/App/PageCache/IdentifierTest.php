<?php
/**
* {license_notice}
*
* @copyright   {copyright}
* @license     {license_link}
*/
namespace Magento\App\PageCache;

class IdentifierTest extends \PHPUnit_Framework_TestCase
{
    public function testGetValue()
    {
        $uri = 'index.php/customer';
        $vary = 1;
        $expected = md5(serialize([$uri, $vary]));

        $requestMock = $this->getMockBuilder('\Magento\App\Request\Http')
            ->disableOriginalConstructor()
            ->getMock();
        $requestMock->expects($this->once())
            ->method('getRequestUri')
            ->will($this->returnValue($uri));
        $requestMock->expects($this->once())
            ->method('get')
            ->with($this->equalTo(\Magento\App\Response\Http::COOKIE_VARY_STRING))
            ->will($this->returnValue($vary));
        $model = new \Magento\App\PageCache\Identifier($requestMock);
        $result = $model->getValue();
        $this->assertEquals($expected, $result);
    }
}
