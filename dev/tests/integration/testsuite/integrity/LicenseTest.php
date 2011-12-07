<?php
/**
 * {license_notice}
 *
 * @category    tests
 * @package     integration
 * @subpackage  integrity
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Integrity_LicenseTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider legacyCommentDataProvider
     */
    public function testLegacyComment($filename)
    {
        $this->markTestSkipped('Problems with build-agent failing because of too many tests.');

        if (!file_exists($filename) || !is_readable($filename)) {
            return;
        }
        $fileText = file_get_contents($filename);
        if (!preg_match_all('#/\*\*.+?\*/#s', $fileText, $matches)) {
            /* There are no PHPDoc comments */
            return;
        }

        foreach ($matches[0] as $commentText) {
            if (strpos($commentText, '@copyright') !== FALSE) {
                foreach (array('Irubin Consulting Inc', 'DBA Varien', 'Magento Inc') as $legacyText) {
                    $this->assertNotContains(
                        $legacyText,
                        $commentText,
                        "License comment must not contain legacy text '$legacyText'."
                    );
                }
            }
        }
    }

    public function legacyCommentDataProvider()
    {
        return array(array(1)); // Dummy to remove unneeded iteration while test is skipped

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator(Mage::getBaseDir())
        );
        $result = array();
        foreach ($iterator as $fileInfo) {
            $filename = (string)$fileInfo;
            if (strpos($filename, DIRECTORY_SEPARATOR . '.git' . DIRECTORY_SEPARATOR)) {
                continue;
            }
            /* Use filename as a data set name to not include it to every assertion message */
            $result[$filename] = array($filename);
        }
        return $result;
    }
}
