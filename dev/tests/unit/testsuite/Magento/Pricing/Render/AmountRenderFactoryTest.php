<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Pricing\Render;

use Magento\TestFramework\Helper\ObjectManager as ObjectManagerHelper;

class AmountRenderFactoryTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\Pricing\Render\AmountRenderFactory */
    protected $amountRenderFactory;

    /** @var ObjectManagerHelper */
    protected $objectManagerHelper;

    /**
     * @var \Magento\View\LayoutInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $layout;

    protected function setUp()
    {
        $this->layout = $this->getMock('Magento\View\LayoutInterface', [], [], '', false);

        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $this->amountRenderFactory = $this->objectManagerHelper->getObject(
            'Magento\Pricing\Render\AmountRenderFactory'
        );
    }

    /**
     * @param string $renderClass
     * @param array $arguments
     * @param string $template
     * @param array $expected
     * @dataProvider createDataProvider
     */
    public function testCreate($renderClass, $arguments, $template, $expected)
    {
        $this->layout->expects($this->once())
            ->method('createBlock')
            ->with($this->equalTo($renderClass), $this->equalTo(''), $this->equalTo($expected['arguments']))
            ->will($this->returnValue($expected['result']));

        $this->assertSame(
            $expected['result'],
            $this->amountRenderFactory->create($this->layout, $renderClass, $template, $arguments)
        );
    }

    /**
     * @return array
     */
    public function createDataProvider()
    {
        return [
            'set new template' => [
                'amount render' => 'Magento\Pricing\Render\Amount',
                'block arguments' => ['some', 'data'],
                'template file' => 'template.phtml',
                'expected result' => [
                    'arguments' => ['some', 'data', 'template' => 'template.phtml'],
                    'result' => $this->getMock('Magento\Pricing\Render\Amount', [], [], '', false)
                ]
            ],
            'template is not set' => [
                'amount render' => 'Magento\Pricing\Render\Amount',
                'block arguments' => ['some', 'data', 'template' => 'other-file.phtml'],
                'template file' => 'template.phtml',
                'expected result' => [
                    'arguments' => ['some', 'data', 'template' => 'other-file.phtml'],
                    'result' => $this->getMock('Magento\Pricing\Render\Amount', [], [], '', false)
                ]
            ],
        ];
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Magento\Pricing\Render\Amount doesn't implement \Magento\Pricing\AmountRenderInterface
     */
    public function testCreateException()
    {
        $object = new \stdClass();
        $this->layout->expects($this->once())
            ->method('createBlock')
            ->with(
                $this->equalTo('Magento\Pricing\Render\Amount'),
                $this->equalTo(''),
                $this->equalTo(['template' => ''])
            )
            ->will($this->returnValue($object));

        $this->amountRenderFactory->create($this->layout);
    }
}
