<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Code_Minify_Adapter_Js_JsminTest extends PHPUnit_Framework_TestCase
{
    public function testMinify()
    {
        if (extension_loaded('jsmin')) {
            $this->markTestSkipped('Use Magento_Code_Minify_Adapter_Js_JsminOptimized adapter for JSMin extension');
        }
        $content = file_get_contents(__DIR__ . '/../../_files/js/original.js');
        $minifier = new Magento_Code_Minify_Adapter_Js_Jsmin();
        $actual = $minifier->minify($content);
        $expected = "\nvar one='one';var two='two';";
        $this->assertEquals($expected, $actual);
    }
}
