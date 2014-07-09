<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Test\Integrity\App\Language;

/**
 * A test for language package declaration
 */
class PackageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param string $file
     * @param string $expectedVendor
     * @param string $expectedCode
     * @dataProvider declaredConsistentlyDataProvider
     */
    public function testDeclaredConsistently($file, $expectedVendor, $expectedCode)
    {
        $dom = new \DOMDocument();
        $dom->load($file);
        $root = $dom->documentElement;
        \Magento\Framework\App\Language\Dictionary::assertVendor($expectedVendor, $root);
        \Magento\Framework\App\Language\Dictionary::assertCode($expectedCode, $root);
    }

    /**
     * @return array
     */
    public function declaredConsistentlyDataProvider()
    {
        $result = [];
        $root = \Magento\TestFramework\Utility\Files::init()->getPathToSource();
        foreach (Package::readDeclarationFiles($root) as $row) {
            $result[] = $row;
        }
        return $result;
    }
}
