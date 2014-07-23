<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Framework\ObjectManager\Code\Generator;

/**
 * Class RepositoryTest
 */
class RepositoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $ioObjectMock;

    /**
     * test setUp
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
     * generate repository name
     */
    public function testGenerate()
    {
        require_once __DIR__ . '/_files/Sample.php';
        $model = $this->getMock(
            'Magento\Framework\ObjectManager\Code\Generator\Repository',
            [
                '_validateData'
            ],
            [
                '\Magento\Framework\ObjectManager\Code\Generator\Sample',
                null,
                $this->ioObjectMock,
                null,
                null
            ]
        );
        $sampleRepositoryCode = file_get_contents(__DIR__ . '/_files/SampleRepository.txt');

        $this->ioObjectMock->expects($this->once())
            ->method('getResultFileName')
            ->with('\Magento\Framework\ObjectManager\Code\Generator\SampleRepository')
            ->will($this->returnValue('SampleRepository.php'));
        $this->ioObjectMock->expects($this->once())
            ->method('writeResultFile')
            ->with(
                $this->equalTo('SampleRepository.php'),
                $this->equalTo($sampleRepositoryCode)
            );

        $model->expects($this->once())->method('_validateData')->will($this->returnValue(true));
        $this->assertTrue($model->generate());
    }
}
