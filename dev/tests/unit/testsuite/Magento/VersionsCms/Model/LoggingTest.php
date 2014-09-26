<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\VersionsCms\Model;

use \Magento\TestFramework\Helper\ObjectManager as ObjectManagerHelper;

class LoggingTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\VersionsCms\Model\Logging
     */
    protected $logging;

    /**
     * @var ObjectManagerHelper
     */
    protected $objectManagerHelper;

    /**
     * @var \Magento\Framework\App\RequestInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $requestInterface;

    /**
     * @var \Magento\Logging\Model\Event|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $eventModel;

    protected function setUp()
    {
        $this->requestInterface = $this->getMock('Magento\Framework\App\RequestInterface');
        $this->eventModel = $this->getMockBuilder('\Magento\Logging\Model\Event')
            ->setMethods(array('setInfo', '__wakeup', '__sleep'))
            ->disableOriginalConstructor()->getMock();
        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $this->logging = $this->objectManagerHelper->getObject(
            'Magento\VersionsCms\Model\Logging',
            [
                'request' => $this->requestInterface
            ]
        );
    }

    public function testPostDispatchCmsHierachyView()
    {
        $this->eventModel->expects($this->once())->method('setInfo')->with('Tree Viewed')->will($this->returnSelf());
        $this->logging->postDispatchCmsHierachyView(array(), $this->eventModel);
    }

    public function testPostDispatchCmsRevisionPreview()
    {
        $this->requestInterface->expects($this->once())->method('getParam')
            ->with('revision_id')
            ->will($this->returnValue('Revision Id'));
        $this->eventModel->expects($this->once())->method('setInfo')->with('Revision Id')->will($this->returnSelf());
        $this->logging->postDispatchCmsRevisionPreview(array(), $this->eventModel);
    }

    public function testPostDispatchCmsRevisionPublish()
    {
        $this->requestInterface->expects($this->once())->method('getParam')
            ->with('revision_id')
            ->will($this->returnValue('Revision Id'));
        $this->eventModel->expects($this->once())->method('setInfo')->with('Revision Id')->will($this->returnSelf());
        $this->logging->postDispatchCmsRevisionPublish(array(), $this->eventModel);
    }
}
