<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Exception;

class LocalizedExceptionTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\Phrase\RendererInterface */
    private $defaultRenderer;

    /** @var string */
    private $renderedMessage;

    public function setUp()
    {
        $this->defaultRenderer = \Magento\Phrase::getRenderer();
        $rendererMock = $this->getMockBuilder('Magento\Phrase\Renderer\Placeholder')
            ->disableOriginalConstructor()
            ->getMock();
        $this->renderedMessage = 'rendered message';
        $rendererMock->expects($this->once())
            ->method('render')
            ->will($this->returnValue($this->renderedMessage));
        \Magento\Phrase::setRenderer($rendererMock);
    }

    public function tearDown()
    {
        \Magento\Phrase::setRenderer($this->defaultRenderer);
    }

    /** @dataProvider constructorParametersDataProvider */
    public function testConstructor($message, $params, $expectedLogMessage)
    {
        $cause = new \Exception();
        $localizeException = new LocalizedException(
            $message,
            $params,
            $cause
        );

        $this->assertEquals(0, $localizeException->getCode());
        $this->assertEquals($this->renderedMessage, $localizeException->getMessage());
        $this->assertEquals($message, $localizeException->getRawMessage());
        $this->assertEquals($expectedLogMessage, $localizeException->getLogMessage());
        $this->assertSame($cause, $localizeException->getPrevious());
    }

    public function constructorParametersDataProvider()
    {
        return [
            'withNoNameParameters' => [
                'message %1 %2',
                ['parameter1',
                 'parameter2'],
                'message parameter1 parameter2',
            ],
            'withNamedParameters'  => [
                'message %key1 %key2',
                ['key1' => 'parameter1',
                 'key2' => 'parameter2'],
                'message parameter1 parameter2',
            ],
            'withoutParameters'    => [
                'message',
                [],
                'message',
                'message',
            ],
        ];
    }
}
