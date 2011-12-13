<?php
/**
 * {license_notice}
 *
 * @category    tests
 * @package     static
 * @subpackage  Legacy
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Legacy_EmailTemplateTest extends PHPUnit_Framework_TestCase
{
    /**
     * @param string $file
     * @dataProvider deprecatedDirectivesDataProvider
     */
    public function testDeprecatedDirectives($file)
    {
        $deprecations = array(
            'htmlescape' => 'use {{escapehtml}} instead',
        );
        $content = file_get_contents($file);
        foreach ($deprecations as $directive => $suggestion) {
            $this->assertNotRegExp(
                '/\{\{' . preg_quote($directive, '/') . '.*?\}\}/i',
                $content,
                "Deprecated directive '$directive' is used, $suggestion."
            );
        }
    }

    public function deprecatedDirectivesDataProvider()
    {
        $globPatterns = array(
            PATH_TO_SOURCE_CODE . '/app/code/*/*/*/view/email/*.html',
            PATH_TO_SOURCE_CODE . '/app/code/*/*/*/view/email/*/*.html',
        );
        $result = array();
        foreach ($globPatterns as $oneGlobPattern) {
            $files = glob($oneGlobPattern);
            foreach ($files as $file) {
                /* Use filename as a data set name to not include it to every assertion message */
                $result[$file] = array($file);
            }
        }
        return $result;
    }
}
