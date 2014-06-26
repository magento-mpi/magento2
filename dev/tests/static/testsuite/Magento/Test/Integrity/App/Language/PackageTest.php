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
    public function testExistingFilesDeclared()
    {
        $root = \Magento\TestFramework\Utility\Files::init()->getPathToSource();
        $notDeclared = array_diff($this->getLangsFromCsvFiles($root), $this->getDeclaredLangs($root));
        $print = print_r($notDeclared, true);
        $this->assertEmpty(
            $notDeclared,
            "There are .csv files in the system that are not declared by any of the language package: {$print}"
        );
    }

    /**
     * Scan code base for .csv files in the expected location of translations and determine distinct list of languages
     *
     * @param string $rootDir
     * @return array
     */
    private function getLangsFromCsvFiles($rootDir)
    {
        $result = [];
        foreach (glob("{$rootDir}/app/{code/*/*,design/*/*/*}/i18n/*.csv", GLOB_BRACE) as $file) {
            $lang = preg_replace('/\.csv$/i', '', basename($file));
            $result[$lang] = $lang;
        }
        return $result;
    }

    /**
     * Scan code base for language.xml files and figure out distinct list of languages from their file structure
     *
     * @param string $rootDir
     * @return array
     */
    private function getDeclaredLangs($rootDir)
    {
        $result = [];
        foreach (self::readDeclarationFiles($rootDir) as $row) {
            $result[$row[2]] = $row[2];
        }
        return $result;
    }

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
        foreach (self::readDeclarationFiles($root) as $row) {
            $result[] = $row;
        }
        return $result;
    }

    /**
     * Read all lamguage.xml files and figure out the vendor and language code according from the file structure
     *
     * @param string $rootDir
     * @return array
     */
    private static function readDeclarationFiles($rootDir)
    {
        $result = [];
        foreach (glob("{$rootDir}/app/i18n/*/*/language.xml") as $file) {
            preg_match('/.+\/(.*)\/(.*)\/language.xml$/', $file, $matches);
            $matches[0] = $file;
            $result[] = $matches;
        }
        return $result;
    }
}
