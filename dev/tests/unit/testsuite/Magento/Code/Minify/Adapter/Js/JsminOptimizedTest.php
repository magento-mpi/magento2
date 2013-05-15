<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Code_Minify_Adapter_Js_JsminOptimizedTest extends PHPUnit_Framework_TestCase
{
    public function testMinify()
    {
        if (!extension_loaded('jsmin')) {
            $this->markTestSkipped('Magento_Code_Minify_Adapter_Js_Jsmin adapter requires JSMin extension');
        }
        $content = file_get_contents(__DIR__ . '/../../_files/js/original.js');
        $minifier = new Magento_Code_Minify_Adapter_Js_JsminOptimized();
        $actual = $minifier->minify($content);
        $expected = "\nvar one='one';var two='two';";
        $this->assertEquals($expected, $actual);
    }
}
