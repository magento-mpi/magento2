<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Framework\Api\Code\Generator;

/**
 * Class BuilderTest
 */
class GenerateBuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $ioObjectMock;

    /**
     * Prepare test env
     */
    protected function setUp()
    {
        $this->ioObjectMock = $this->getMock(
            '\Magento\Framework\Code\Generator\Io',
            [],
            [],
            '',
            false
        );
    }

    /**
     * generate repository class
     */
    public function testGenerate()
    {
        require_once __DIR__ . '/_files/Sample.php';
        /** @var \Magento\Framework\Api\Code\Generator\Builder $model */
        $model = $this->getMock(
            '\Magento\Framework\Api\Code\Generator\Builder',
            [
                '_validateData'
            ],
            [
                '\Magento\Framework\Api\Code\Generator\Sample',
                null,
                $this->ioObjectMock,
                null,
                null
            ]
        );
        $sampleBuilderCode = file_get_contents(__DIR__ . '/_files/SampleBuilder.txt');
        $this->ioObjectMock->expects($this->once())
            ->method('getResultFileName')
            ->with('\Magento\Framework\Api\Code\Generator\SampleBuilder')
            ->will($this->returnValue('SampleBuilder.php'));
        $this->ioObjectMock->expects($this->once())
            ->method('writeResultFile')
            ->with('SampleBuilder.php', $sampleBuilderCode);

        $model->expects($this->once())
            ->method('_validateData')
            ->will($this->returnValue(true));
        $this->assertTrue($model->generate());
    }
}
