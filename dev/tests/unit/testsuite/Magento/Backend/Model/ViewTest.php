<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Model;

class ViewTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Backend\Model\View
     */
    protected $_view;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_layoutMock;

    protected function setUp()
    {
        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $aclFilter = $this->getMock('Magento\Backend\Model\Layout\Filter\Acl', [], [], '', false);
        $this->_layoutMock = $this->getMock('Magento\Framework\View\Layout', [], [], '', false);
        $layoutProcessor = $this->getMock('Magento\Core\Model\Layout\Merge', [], [], '', false);
        $configMock = $this->getMock('Magento\Framework\View\Page\Config', [], [], '', false);

        $node = new \Magento\Framework\Simplexml\Element('<node/>');
        $this->_layoutMock->expects($this->once())->method('getNode')->will($this->returnValue($node));
        $this->_layoutMock->expects($this->any())->method('getUpdate')->will($this->returnValue($layoutProcessor));

        $resultPage = $this->getMockBuilder('Magento\Framework\View\Result\Page')
            ->disableOriginalConstructor()
            ->setMethods(['getLayout', 'getDefaultLayoutHandle', 'getConfig'])
            ->getMock();
        $resultPage->expects($this->any())
            ->method('getConfig')
            ->will($this->returnValue($configMock));
        $resultPage->expects($this->atLeastOnce())
            ->method('getLayout')
            ->will($this->returnValue($this->_layoutMock));
        $pageFactory = $this->getMockBuilder('Magento\Framework\View\Result\PageFactory')
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $pageFactory->expects($this->once())
            ->method('create')
            ->will($this->returnValue($resultPage));

        $this->_view = $helper->getObject(
            'Magento\Backend\Model\View',
            array(
                'aclFilter' => $aclFilter,
                'layout' => $this->_layoutMock,
                'request' => $this->getMock('Magento\Framework\App\Request\Http', array(), array(), '', false),
                'pageFactory' => $pageFactory
            )
        );
    }

    public function testLoadLayoutWhenBlockIsGenerate()
    {
        $this->_layoutMock->expects($this->once())->method('generateElements');
        $this->_view->loadLayout();
    }

    public function testLoadLayoutWhenBlockIsNotGenerate()
    {
        $this->_layoutMock->expects($this->never())->method('generateElements');
        $this->_view->loadLayout(null, false, true);
    }
}
