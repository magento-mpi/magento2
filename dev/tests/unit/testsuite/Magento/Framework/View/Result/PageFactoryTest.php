<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Framework\View\Result;

use Magento\TestFramework\Helper\ObjectManager as ObjectManagerHelper;

class PageFactoryTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\Framework\View\Result\PageFactory */
    protected $pageFactory;

    /** @var \Magento\Framework\View\Result\Page|\PHPUnit_Framework_MockObject_MockObject */
    protected $page;

    /** @var ObjectManagerHelper */
    protected $objectManagerHelper;

    /** @var \Magento\Framework\ObjectManager|\PHPUnit_Framework_MockObject_MockObject */
    protected $objectManagerMock;

    protected function setUp()
    {
        $this->objectManagerMock = $this->getMock('Magento\Framework\ObjectManager', [], [], '', false);
        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $this->pageFactory = $this->objectManagerHelper->getObject(
            'Magento\Framework\View\Result\PageFactory',
            [
                'objectManager' => $this->objectManagerMock
            ]
        );
        $this->page = $this->objectManagerHelper->getObject(
            'Magento\Framework\View\Result\Page'
        );
    }

    public function testCreate()
    {
        $this->objectManagerMock->expects($this->once())->method('create')->with('\Magento\Framework\View\Result\Page')
            ->will($this->returnValue($this->page));
        $this->assertInstanceOf('Magento\Framework\View\Result\Page', $this->pageFactory->create());
    }
}
