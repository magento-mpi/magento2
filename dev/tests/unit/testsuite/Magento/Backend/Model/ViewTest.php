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
        $aclFilter = $this->getMock('Magento\Backend\Model\Layout\Filter\Acl', array(), array(), '', false);
        $this->_layoutMock = $this->getMock('Magento\Core\Model\Layout', array(), array(), '', false);
        $layoutProcessor = $this->getMock('Magento\View\Layout\ProcessorInterface');
        $node = new \Magento\Simplexml\Element('<node/>');
        $this->_layoutMock->expects($this->once())->method('getNode')->will($this->returnValue($node));
        $this->_layoutMock->expects($this->any())->method('getUpdate')->will($this->returnValue($layoutProcessor));
        $this->_view = $helper->getObject(
            'Magento\Backend\Model\View',
            array(
                'aclFilter' => $aclFilter,
                'layout' => $this->_layoutMock,
                'request' => $this->getMock('Magento\App\Request\Http', array(), array(), '', false)
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
