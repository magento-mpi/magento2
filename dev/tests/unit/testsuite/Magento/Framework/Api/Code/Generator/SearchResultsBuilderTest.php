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
 * Class MapperTest
 */
class SearchResultsBuilderTest extends EntityChildTestAbstract
{
    const SOURCE_CLASS_NAME = 'Magento\Framework\Api\Code\Generator\Sample';
    const RESULT_CLASS_NAME = 'Magento\Framework\Api\Code\Generator\SampleSearchResultsBuilder';
    const GENERATOR_CLASS_NAME = 'Magento\Framework\Api\Code\Generator\SearchResultsBuilder';
    const OUTPUT_FILE_NAME = 'SampleSearchResultsBuilder.php';

    protected function getSourceClassName()
    {
        return self::SOURCE_CLASS_NAME;
    }

    protected function getResultClassName()
    {
        return self::RESULT_CLASS_NAME;
    }

    protected function getGeneratorClassName()
    {
        return self::GENERATOR_CLASS_NAME;
    }

    protected function getOutputFileName()
    {
        return self::OUTPUT_FILE_NAME;
    }

    /**
     * generate repository name
     */
    public function testGenerate()
    {
        $generatedCode = 'Generated code';
        $resultFileName = self::OUTPUT_FILE_NAME;

        $this->mockDefinedClassesCall();

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
            ->method('setExtendedClass')
            ->with(SearchResultsBuilder::SEARCH_RESULT_BUILDER)
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

        $this->assertEquals(
            $resultFileName,
            $this->generator->generate(),
            implode("\n", $this->generator->getErrors())
        );
    }
}
