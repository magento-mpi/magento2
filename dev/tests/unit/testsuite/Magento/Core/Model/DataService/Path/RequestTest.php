<?php
/**
 * \Magento\Core\Model\DataService\Path\Request
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\DataService\Path;

class RequestTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Test data for params
     */
    const SOME_INTERESTING_PARAMS = 'Some interesting params.';

    public function testGetChild()
    {
        $requestMock = $this->getMockBuilder('Magento\Core\Controller\Request\Http')
            ->disableOriginalConstructor()
            ->getMock();
        $requestMock->expects($this->once())
            ->method('getParams')
            ->will($this->returnValue(self::SOME_INTERESTING_PARAMS));
        $requestVisitor = new \Magento\Core\Model\DataService\Path\Request($requestMock);
        $this->assertEquals(self::SOME_INTERESTING_PARAMS, $requestVisitor->getChildNode('params'));
    }

    public function testNotFound()
    {
        $requestMock = $this->getMockBuilder('Magento\Core\Controller\Request\Http')->disableOriginalConstructor()
            ->getMock();

        $requestVisitor = new \Magento\Core\Model\DataService\Path\Request($requestMock);
        $this->assertEquals(null, $requestVisitor->getChildNode('foo'));
    }
}
