<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Url;

class CssResolverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\View\Url\CssResolver
     */
    protected $object;

    protected function setUp()
    {
        $this->object = new CssResolver();
    }

    public function testRelocateRelativeUrls()
    {
        $relatedPath = '/some/directory/two/another/file.ext';
        $filePath = '/some/directory/one/file.ext';

        $fixturePath = __DIR__ . '/_files/';
        $source = file_get_contents($fixturePath . 'source.css');
        $result = file_get_contents($fixturePath . 'resultNormalized.css');

        $this->assertEquals($result, $this->object->relocateRelativeUrls($source, $relatedPath, $filePath));
    }

    /**
     * @param string $cssContent
     * @param string $expectedResult
     * @dataProvider aggregateImportDirectivesDataProvider
     */
    public function testAggregateImportDirectives($cssContent, $expectedResult)
    {
        $this->assertEquals($expectedResult, $this->object->aggregateImportDirectives($cssContent));
    }

    /**
     * @return array
     */
    public function aggregateImportDirectivesDataProvider()
    {
        $fixturePath = __DIR__ . '/_files/';
        $source = file_get_contents($fixturePath . 'sourceImport.css');
        $result = file_get_contents($fixturePath . 'resultImport.css');
        $sourceNoImport = 'li {background: url("https://example.com/absolute.gif");}';

        return array(
            'empty' => array('', ''),
            'data without patterns' => array($sourceNoImport, $sourceNoImport),
            'data with patterns' => array($source, $result)
        );
    }

    /**
     * @param $cssContent
     * @param $inlineCallback
     * @param $expectedResult
     * @dataProvider replaceRelativeUrlsDataProvider
     */
    public function testReplaceRelativeUrls($cssContent, $inlineCallback, $expectedResult)
    {
        $actual = $this->object->replaceRelativeUrls($cssContent, $inlineCallback);
        $this->assertEquals($expectedResult, $actual);
    }

    /**
     * @return array
     */
    public static function replaceRelativeUrlsDataProvider()
    {
        $fixturePath = __DIR__ . '/_files/';
        $callback = function ($relativeUrl) {
            return '../two/another/' . $relativeUrl;
        };

        $source = file_get_contents($fixturePath . 'source.css');
        $result = file_get_contents($fixturePath . 'result.css');
        $sourceNoPatterns = 'li {background: url("https://example.com/absolute.gif");}';

        return array(
            'empty' => array(
                '',
                function () {
                },
                ''
            ),
            'data without patterns' => array($sourceNoPatterns, $callback, $sourceNoPatterns),
            'data with patterns' => array($source, $callback, $result)
        );
    }
}
