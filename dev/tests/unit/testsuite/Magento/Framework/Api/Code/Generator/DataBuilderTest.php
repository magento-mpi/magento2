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
class DataBuilderTest extends EntityChildTestAbstract
{
    /*
     * The test is based on assumption that the classes will be injecting "DataBuilder" as dependency which will
     * indicate the compiler to identify and code generate based on ExtensibleSample implementations' interface
     */
    const SOURCE_CLASS_NAME = 'Magento\Framework\Api\Code\Generator\ExtensibleSample';
    const RESULT_CLASS_NAME = 'Magento\Framework\Api\Code\Generator\ExtensibleSampleDataBuilder';
    const GENERATOR_CLASS_NAME = 'Magento\Framework\Api\Code\Generator\DataBuilder';
    const OUTPUT_FILE_NAME = 'ExtensibleSampleDataBuilder.php';

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

    protected function setUp()
    {
        parent::setUp();

        require_once __DIR__ . '/_files/ExtensibleSampleInterface.php';
        require_once __DIR__ . '/_files/ExtensibleSample.php';

    }

    protected function mockDefinedClassesCall()
    {
        $this->definedClassesMock->expects($this->at(0))
            ->method('classLoadable')
            ->with($this->getSourceClassName() . 'Interface')
            ->willReturn(true);
    }
}
