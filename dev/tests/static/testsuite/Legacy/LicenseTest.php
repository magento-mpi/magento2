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

class Legacy_LicenseTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider legacyCommentDataProvider
     */
    public function testLegacyComment($filename)
    {
        $fileText = file_get_contents($filename);
        if (!preg_match_all('#/\*\*.+@copyright.+?\*/#s', $fileText, $matches)) {
            return;
        }

        foreach ($matches[0] as $commentText) {
            foreach (array('Irubin Consulting Inc', 'DBA Varien', 'Magento Inc') as $legacyText) {
                $this->assertNotContains(
                    $legacyText,
                    $commentText,
                    "The license of file {$filename} contains legacy text."
                );
            }
        }
    }

    public function legacyCommentDataProvider()
    {
        $recursiveIterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(
            PATH_TO_SOURCE_CODE, FilesystemIterator::SKIP_DOTS | FilesystemIterator::UNIX_PATHS
        ));

        $rootFolderName = substr(strrchr(PATH_TO_SOURCE_CODE, DIRECTORY_SEPARATOR), 1);
        $extensions = '(xml|css|php|phtml|js|dist|sample|additional)';
        $paths =  array(
            $rootFolderName . '/[^/]+\.' . $extensions,
            $rootFolderName . '/app/.+\.' . $extensions,
            $rootFolderName . '/dev/(?!tests/integration/tmp).+\.' . $extensions,
            $rootFolderName . '/downloader/.+\.' . $extensions,
            $rootFolderName . '/lib/(Mage|Magento|Varien)/.+\.' . $extensions,
            $rootFolderName . '/pub/.+\.' . $extensions,
        );
        $regexIterator = new RegexIterator($recursiveIterator, '#(' . implode(' | ', $paths) . ')$#x');

        $result = array();
        foreach ($regexIterator as $fileInfo) {
            $filename = (string)$fileInfo;
            if (!file_exists($filename) || !is_readable($filename)) {
                continue;
            }
            $result[] = array($filename);
        }
        return $result;
    }
}
