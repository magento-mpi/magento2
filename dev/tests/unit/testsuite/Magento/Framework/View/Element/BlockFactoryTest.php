<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\View\Element;

class BlockFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\View\Element\BlockFactory
     */
    protected $blockFactory;

    /**
     * @var \Magento\Framework\ObjectManager|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $objectManagerMock;

    public function setUp()
    {
        $objectManagerHelper = new \Magento\TestFramework\Helper\ObjectManager($this);

        $this->objectManagerMock = $this->getMockBuilder('Magento\Framework\ObjectManager')
            ->disableOriginalConstructor()
            ->getMock();

        $this->blockFactory = $objectManagerHelper->getObject('Magento\Framework\View\Element\BlockFactory', array(
            'objectManager' => $this->objectManagerMock
        ));
    }

    public function testCreateBlock()
    {
        $className = 'Magento\Framework\View\Element\Template';
        $argumentsResult = ['arg1', 'arg2'];

        $templateMock = $this->getMockBuilder('Magento\Framework\View\Element\Template')
            ->disableOriginalConstructor()->getMock();

        $this->objectManagerMock->expects($this->once())
            ->method('create')
            ->with($className, $argumentsResult)
            ->will($this->returnValue($templateMock));

        $this->assertInstanceOf(
            'Magento\Framework\View\Element\BlockInterface',
            $this->blockFactory->createBlock($className, $argumentsResult)
        );
    }

    /**
     * @expectedException \LogicException
     */
    public function testCreateBlockWithException()
    {
        $this->blockFactory->createBlock('invalid_class_name');
    }
}
