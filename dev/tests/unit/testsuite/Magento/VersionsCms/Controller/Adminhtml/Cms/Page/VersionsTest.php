<?php
/**
 * Tests Magento\VersionsCms\Controller\Adminhtml\Cms\Page\Versions
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\VersionsCms\Controller\Adminhtml\Cms\Page\Version;
use Magento\TestFramework\Helper\ObjectManager;

class  VersionsTest extends \PHPUnit_Framework_TestCase
{
    public function testExecute()
    {
        $viewMock = $this->basicMock('\Magento\Framework\App\ViewInterface');
        $pageLoaderMock = $this->basicMock('\Magento\VersionsCms\Model\PageLoader');

        // Context Mock
        $contextMock = $this->basicMock('\Magento\Backend\App\Action\Context');
        $this->basicStub($contextMock, 'getView')->willReturn($viewMock);
        $this->basicStub($contextMock, 'getRequest')
            ->willReturn($this->basicMock('Magento\Framework\App\RequestInterface'));
        $this->basicStub($contextMock, 'getResponse')
            ->willReturn($this->basicMock('Magento\Framework\App\ResponseInterface'));
        $this->basicStub($contextMock, 'getTitle')
            ->willReturn($this->basicMock('Magento\Framework\App\Action\Title'));

        // SUT
        $mocks = [
            'context' => $contextMock,
            'pageLoader' => $pageLoaderMock,
        ];
        $objectManager = new ObjectManager($this);
        $model = $objectManager->getObject('Magento\VersionsCms\Controller\Adminhtml\Cms\Page\Versions', $mocks);

        // Expectations and test
        $viewMock->expects($this->once())
            ->method('loadLayout');
        $viewMock->expects($this->once())
            ->method('renderLayout');
        $pageLoaderMock->expects($this->once())
            ->method('load');

        $model->execute();
    }

    /**
     * @param \PHPUnit_Framework_MockObject_MockObject $mock
     * @param string $method
     *
     * @return \PHPUnit_Framework_MockObject_Builder_InvocationMocker
     */
    private function basicStub($mock, $method)
    {
        return $mock->expects($this->any())
            ->method($method)
            ->withAnyParameters();
    }

    /**
     * @param string $className
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function basicMock($className)
    {
        return $this->getMockBuilder($className)
            ->disableOriginalConstructor()
            ->getMock();
    }

} 