<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Framework\Api\Code\Generator;

use Magento\Framework\Code\Generator\Io;
use Magento\TestFramework\Helper\ObjectManager;

/**
 * Class BuilderTest
 */
class BuilderTest extends \PHPUnit_Framework_TestCase
{
    const SOURCE_CLASS_NAME = 'Magento\Framework\Api\Code\Generator\Sample';
    const RESULT_CLASS_NAME = 'Magento\Framework\Api\Code\Generator\SampleBuilder';
    const GENERATOR_CLASS_NAME = 'Magento\Framework\Api\Code\Generator\Builder';
    const OUTPUT_FILE_NAME = 'SampleBuilder.php';
    /**
     * @var Io | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $ioObjectMock;

    /**
     * @var \Magento\Framework\Code\Generator\EntityAbstract
     */
    protected $generator;

    /**
     * @var \Magento\Framework\Code\Generator\FileResolver | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $fileResolverMock;

    /**
     * @var \Magento\Framework\Code\Generator\CodeGenerator\Zend | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $classGenerator;

    protected function setUp()
    {
        require_once __DIR__ . '/_files/Sample.php';
        $this->ioObjectMock = $this->getMock(
            'Magento\Framework\Code\Generator\Io',
            [],
            [],
            '',
            false
        );
        $this->fileResolverMock = $this->getMock(
            'Magento\Framework\Code\Generator\FileResolver',
            [],
            [],
            '',
            false
        );
        $this->classGenerator = $this->getMock(
            'Magento\Framework\Code\Generator\CodeGenerator\Zend',
            [],
            [],
            '',
            false
        );

        $objectManager = new ObjectManager($this);
        $this->generator = $objectManager->getObject(
            self::GENERATOR_CLASS_NAME,
            [
                'sourceClassName' => self::SOURCE_CLASS_NAME,
                'resultClassName' => self::RESULT_CLASS_NAME,
                'ioObject' => $this->ioObjectMock,
                'classGenerator' => $this->classGenerator,
                'fileResolver' => $this->fileResolverMock
            ]
        );
    }

    /**
     * generate repository name
     */
    public function testGenerate()
    {
        $generatedCode = 'Generated code';
        $sourceFileName = 'Sample.php';
        $resultFileName = self::OUTPUT_FILE_NAME;

        //Mocking _validateData call
        $this->fileResolverMock->expects($this->at(0))
            ->method('getFile')
            ->with(self::SOURCE_CLASS_NAME)
            ->will($this->returnValue($sourceFileName));
        $this->fileResolverMock->expects($this->at(1))
            ->method('getFile')
            ->with(self::RESULT_CLASS_NAME)
            ->will($this->returnValue(false));

        $this->ioObjectMock->expects($this->once())
            ->method('makeGenerationDirectory')
            ->will($this->returnValue(true));
        $this->ioObjectMock->expects($this->once())
            ->method('makeResultFileDirectory')
            ->with(self::RESULT_CLASS_NAME)
            ->will($this->returnValue(true));
        $this->ioObjectMock->expects($this->once())
            ->method('fileExists')
            ->with($resultFileName)
            ->will($this->returnValue(false));

        //Mocking _generateCode call
        $this->classGenerator->expects($this->once())
            ->method('setName')
            ->with(self::RESULT_CLASS_NAME)
            ->will($this->returnSelf());
        $this->classGenerator->expects($this->once())
            ->method('addProperties')
            ->will($this->returnSelf());
        $this->classGenerator->expects($this->once())
            ->method('addMethods')
            ->will($this->returnSelf());
        $this->classGenerator->expects($this->once())
            ->method('setClassDocBlock')
            ->will($this->returnSelf());
        $this->classGenerator->expects($this->once())
            ->method('generate')
            ->will($this->returnValue($generatedCode));

        //Mocking generation
        $this->ioObjectMock->expects($this->any())
            ->method('getResultFileName')
            ->with(self::RESULT_CLASS_NAME)
            ->will($this->returnValue($resultFileName));
        $this->ioObjectMock->expects($this->once())
            ->method('writeResultFile')
            ->with($resultFileName, $generatedCode);

        $this->assertTrue($this->generator->generate());
    }
}
