<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Controller\Adminhtml\Category\Widget;

class ChooserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Catalog\Controller\Adminhtml\Category\Widget
     */
    protected $controller;

    /**
     * @var \Magento\Framework\App\Response\Http|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $responseMock;

    /**
     * @var \Magento\Framework\App\Request\Http|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $requestMock;

    /**
     * @var \Magento\Backend\Model\View|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $viewMock;

    /**
     * @var \Magento\Catalog\Block\Adminhtml\Category\Widget\Chooser|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $chooserBlockMock;

    /**
     * @var \Magento\Core\Model\Layout|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $layoutMock;

    /**
     * @var \Magento\TestFramework\Helper\ObjectManager|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $objectManagerMock;

    public function setUp()
    {
        $this->responseMock = $this->getMock('Magento\Framework\App\Response\Http', array(), array(), '', false);
        $this->requestMock = $this->getMock('Magento\Framework\App\Request\Http', array(), array(), '', false);
        $this->viewMock = $this->getMock('Magento\Backend\Model\View', array('getLayout'), array(), '', false);
        $this->objectManagerMock = $this->getMock(
            'Magento\Framework\ObjectManager\ObjectManager',
            array(),
            array(),
            '',
            false
        );
        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);

        $context = $this->getMock(
            'Magento\Backend\App\Action\Context',
            array('getRequest', 'getResponse', 'getMessageManager', 'getSession'),
            $helper->getConstructArguments(
                'Magento\Backend\App\Action\Context',
                array(
                    'response' => $this->responseMock,
                    'request' => $this->requestMock,
                    'view' => $this->viewMock,
                    'objectManager' => $this->objectManagerMock
                )
            )
        );
        $context->expects($this->once())->method('getRequest')->will($this->returnValue($this->requestMock));
        $context->expects($this->once())->method('getResponse')->will($this->returnValue($this->responseMock));
        $this->controller = new \Magento\Catalog\Controller\Adminhtml\Category\Widget\Chooser(
            $context
        );
    }

    protected function _getTreeBlock()
    {
        $this->chooserBlockMock = $this->getMock(
            'Magento\Catalog\Block\Adminhtml\Category\Widget\Chooser', array(), array(), '', false
        );
        $this->layoutMock = $this->getMock('Magento\Core\Model\Layout', array('createBlock'), array(), '', false);
        $this->layoutMock->expects($this->once())->method('createBlock')->will(
            $this->returnValue($this->chooserBlockMock)
        );
        $this->viewMock->expects($this->once())->method('getLayout')->will($this->returnValue($this->layoutMock));
    }

    public function testExecute()
    {
        $this->_getTreeBlock();
        $testHtml = '<div>Some test html</div>';
        $this->chooserBlockMock->expects($this->once())->method('toHtml')->will($this->returnValue($testHtml));
        $this->responseMock->expects($this->once())->method('setBody')->with($this->equalTo($testHtml));
        $this->controller->execute();
    }
}
