<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Cms\Block\Adminhtml\Block;

/**
 * @covers \Magento\Cms\Block\Adminhtml\Block\Edit
 */
class EditTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Cms\Block\Adminhtml\Block\Edit
     */
    protected $this;

    /**
     * @var \Magento\Framework\Registry|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $registryMock;

    /**
     * @var \Magento\Cms\Model\Block|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $modelBlockMock;

    protected function setUp()
    {
        $this->registryMock = $this
            ->getMockBuilder('Magento\Framework\Registry')
            ->disableOriginalConstructor()
            ->getMock();
        $this->modelBlockMock = $this
            ->getMockBuilder('Magento\Cms\Model\Block')
            ->disableOriginalConstructor()
            ->getMock();

        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->this = $objectManager->getObject(
            'Magento\Cms\Block\Adminhtml\Block\Edit',
            [
                'registry' => $this->registryMock
            ]
        );
    }

    /**
     * @covers \Magento\Cms\Block\Adminhtml\Block\Edit::getHeaderText
     */
    public function testGetHeaderText()
    {
        $this->registryMock
            ->expects($this->any())
            ->method('registry')
            ->with('cms_block')
            ->willReturn($this->modelBlockMock);

        $this->assertInternalType('string', $this->this->getHeaderText());
    }
}
