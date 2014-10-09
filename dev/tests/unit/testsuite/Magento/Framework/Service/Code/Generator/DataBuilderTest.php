<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Framework\Service\Code\Generator;

use Magento\Framework\Code\Generator\Io;
use Magento\TestFramework\Helper\ObjectManager;

/**
 * Class BuilderTest
 */
class DataBuilderTest extends \PHPUnit_Framework_TestCase
{
    /*
     * The test is based on assumption that the classes will be injecting "DataBuilder" as dependency which will
     * indicate the compiler to identify and code generate based on SampleData implementations' interface
     */
    const SOURCE_CLASS_NAME = 'Magento\Framework\Service\Code\Generator\SampleData';
    const RESULT_CLASS_NAME = 'Magento\Framework\Service\Code\Generator\SampleDataBuilder';
    const GENERATOR_CLASS_NAME = 'Magento\Framework\Service\Code\Generator\DataBuilder';
    const OUTPUT_FILE_NAME = 'SampleDataBuilder.php';
    /**
     * @var Io | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $ioObjectMock;

    /**
     * @var \Magento\Framework\Code\Generator\EntityAbstract
     */
    protected $generator;

    /**
     * @var \Magento\Framework\Autoload\IncludePath | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $autoloaderMock;

    /**
     * @var \Magento\Framework\Code\Generator\CodeGenerator\Zend | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $classGenerator;

    protected function setUp()
    {
        require_once __DIR__ . '/_files/SampleDataInterface.php';
        require_once __DIR__ . '/_files/SampleData.php';
        $this->ioObjectMock = $this->getMock(
            'Magento\Framework\Code\Generator\Io',
            [],
            [],
            '',
            false
        );
        $this->autoloaderMock = $this->getMock(
            'Magento\Framework\Autoload\IncludePath',
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


        $this->generator = $objectManager->getObject(
            self::GENERATOR_CLASS_NAME,
            [
                'sourceClassName' => self::SOURCE_CLASS_NAME,
                'resultClassName' => self::RESULT_CLASS_NAME,
                'ioObject' => $this->ioObjectMock,
                'classGenerator' => $this->classGenerator,
                'autoLoader' => $this->autoloaderMock
            ]
        );
    }

    /**
     * generate repository name
     */
    public function testGenerate()
    {
        $this->markTestIncomplete('Incomplete feature');
        $generatedCode = 'Generated code';
        $sourceFileName = 'SampleData.php';
        $resultFileName = self::OUTPUT_FILE_NAME;

        //Mocking _validateData call
        $this->autoloaderMock->expects($this->at(0))
            ->method('getFile')
            ->with(self::SOURCE_CLASS_NAME)
            ->will($this->returnValue($sourceFileName));
        $this->autoloaderMock->expects($this->at(1))
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
        /*
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
        */

        //Mocking generation
        $this->ioObjectMock->expects($this->any())
            ->method('getResultFileName')
            ->with(self::RESULT_CLASS_NAME)
            ->will($this->returnValue($resultFileName));
        $this->ioObjectMock->expects($this->once())
            ->method('writeResultFile')
            ->with($resultFileName, $generatedCode);

        //$this->assertTrue($this->generator->generate());
    }
}
