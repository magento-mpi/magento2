<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Framework\Service\Code\Generator;

use Magento\Framework\Service\Code\Generator\SearchResults;

/**
 * Class SearchResultTest
 */
class GenerateSearchResultsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $ioObjectMock;

    /**
     * Create mock for class \Magento\Framework\Code\Generator\Io
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
     * Generate SearchResult class
     */
    public function testGenerate()
    {
        require_once __DIR__ . '/_files/Sample.php';
        $model = $this->getMock(
            'Magento\Framework\Service\Code\Generator\SearchResults',
            [
                '_validateData'
            ],
            [
                '\Magento\Framework\Service\Code\Generator\Sample',
                null,
                $this->ioObjectMock,
                null,
                null
            ]
        );
        $sampleSearchResultBuilderCode = file_get_contents(__DIR__ . '/_files/SampleSearchResults.txt');
        $this->ioObjectMock->expects($this->once())
            ->method('getResultFileName')
            ->with('\Magento\Framework\Service\Code\Generator\SampleSearchResults')
            ->will($this->returnValue('SampleSearchResults.php'));
        $this->ioObjectMock->expects($this->once())
            ->method('writeResultFile')
            ->with('SampleSearchResults.php', $sampleSearchResultBuilderCode);

        $model->expects($this->once())
            ->method('_validateData')
            ->will($this->returnValue(true));
        $this->assertTrue($model->generate());
    }

    /**
     * test protected _validateData()
     */
    public function testValidateData()
    {
        $sourceClassName = 'Magento_Module_Controller_Index';
        $resultClassName = 'Magento_Module_Controller';

        $includePathMock = $this->getMockBuilder('Magento\Framework\Autoload\IncludePath')
            ->disableOriginalConstructor()
            ->setMethods(['getFile'])
            ->getMock();
        $includePathMock->expects($this->at(0))
            ->method('getFile')
            ->with($sourceClassName)
            ->will($this->returnValue(true));
        $includePathMock->expects($this->at(1))
            ->method('getFile')
            ->with($resultClassName)
            ->will($this->returnValue(false));

        $searchResults = new SearchResults(
            null, null, null, null, $includePathMock
        );
        $searchResults->init($sourceClassName, $resultClassName);
        $this->assertFalse($searchResults->generate());
    }
}
