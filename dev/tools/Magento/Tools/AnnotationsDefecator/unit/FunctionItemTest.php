<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tools\AnnotationsDefecator\unit;

require __DIR__ . '/../../../../bootstrap.php';

use Magento\Tools\AnnotationsDefecator\Line\FunctionClassItem;

class FunctionItemTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param string $content
     * @param bool $isFunctionLine
     * @dataProvider contentDataProvider
     */
    public function testIsFunctionLine($content, $isFunctionLine)
    {
        $this->assertEquals($isFunctionLine, FunctionClassItem::isFunctionClassItem($content));
    }

    /**
     * @return array
     */
    public function contentDataProvider()
    {
        return [
            ['public function testA1($par1, $par2, array $par3, $par4 = \'\')', true],
            ['private function _testA1($par1, $par2, array $par3, $par4 = \'\')', true],
            ['protected function _testA1($par1, $par2, array $par3, $par4 = \'\')', true],
            ['public static function testA1($par1, $par2, array $par3, $par4 = \'\')', true],
            ['private static function testA1($par1, $par2, array $par3, $par4 = \'\')', true],
            ['protected static function testA1($par1, $par2, array $par3, $par4 = \'\')', true],
            ['static public function testA1($par1, $par2, array $par3, $par4 = \'\')', true],
            ['static private function _testA1($par1, $par2, array $par3, $par4 = \'\')', true],
            ['static protected function _testA1($par1, $par2, array $par3, $par4 = \'\')', true],
            ['static function testA1($par1, $par2, array $par3, $par4 = \'\')', true],
            ['  function testA1()', true],
            ['class Lala', true],
            ['interface Lolo', true],
            ['  class Lala', true],
            ['  interface Lolo', true],
            ['abstract class Lala', true],
            [' abstract class Lala', true],
            ['public function __blabla(', true],
            ['final class PHPParser_Parser', true],
            ['final public function testA1($par1, $par2, array $par3, $par4 = \'\')', true],
            ['final protected function testA1($par1, $par2, array $par3, $par4 = \'\')', true]
        ];
    }
}
