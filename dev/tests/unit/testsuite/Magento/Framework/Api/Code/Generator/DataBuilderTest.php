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
class DataBuilderTest extends \PHPUnit_Framework_TestCase
{
    /*
     * The test is based on assumption that the classes will be injecting "DataBuilder" as dependency which will
     * indicate the compiler to identify and code generate based on ExtensibleSample implementations' interface
     */
    const SOURCE_CLASS_NAME = 'Magento\Framework\Api\Code\Generator\ExtensibleSample';
    const RESULT_CLASS_NAME = 'Magento\Framework\Api\Code\Generator\ExtensibleSampleDataBuilder';
    const GENERATOR_CLASS_NAME = 'Magento\Framework\Api\Code\Generator\DataBuilder';
    const OUTPUT_FILE_NAME = 'ExtensibleSampleDataBuilder.php';
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

    /** @var \Magento\Framework\App\ObjectManager|\PHPUnit_Framework_MockObject_MockObject */
    protected $objectManagerMock;

    protected function setUp()
    {
        require_once __DIR__ . '/_files/ExtensibleSampleInterface.php';
        require_once __DIR__ . '/_files/ExtensibleSample.php';
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
        $objectManager = new ObjectManager($this);
        $this->classGenerator = $objectManager->getObject('Magento\Framework\Code\Generator\CodeGenerator\Zend');
            $this->getMock(
            'Magento\Framework\Code\Generator\CodeGenerator\Zend',
            [],
            [],
            '',
            false
        );

        $this->objectManagerMock = $this->getMock('Magento\Framework\App\ObjectManager', [], [], '', false);
        \Magento\Framework\App\ObjectManager::setInstance($this->objectManagerMock);

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
        $generatedCode = file_get_contents(__DIR__ . '/_files/ExtensibleSampleDataBuilder.txt');
        $sourceFileName = 'ExtensibleSample.php';
        $resultFileName = self::OUTPUT_FILE_NAME;

        //Mocking _validateData call
        $this->fileResolverMock->expects($this->at(0))
            ->method('getFile')
            ->with(self::SOURCE_CLASS_NAME . "Interface")
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

        //Mocking generation
        $this->ioObjectMock->expects($this->any())
            ->method('getResultFileName')
            ->with(self::RESULT_CLASS_NAME)
            ->will($this->returnValue($resultFileName));

        //Verify if the generated code is as expected
        $this->ioObjectMock->expects($this->once())
            ->method('writeResultFile')
            ->with($resultFileName, $generatedCode);

        $this->assertTrue($this->generator->generate());
    }
}
