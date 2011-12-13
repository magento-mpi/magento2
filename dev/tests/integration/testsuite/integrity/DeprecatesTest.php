<?php
/**
 * Integrity test used to check, that files do not contain the code from removed deprecates
 *
 * {license_notice}
 *
 * @category    tests
 * @package     integration
 * @subpackage  integrity
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Integrity_DeprecatesTest extends Magento_Test_TestCase_VisitorAbstract
{
    /**
     * @return void
     */
    public function testFindDeprecatedStuff()
    {
        $found = $this->_findDeprecates();
        $this->assertEmpty($found, implode(".\n", $found));
    }

    /**
     * Gathers all deprecated or removed stuff
     *
     * @return array
     */
    protected function _findDeprecates()
    {
        $directory  = new RecursiveDirectoryIterator(Mage::getRoot());
        $iterator = new RecursiveIteratorIterator($directory);
        $regexIterator = new RegexIterator($iterator, '/(\.php|\.phtml|\.xml|\.js|\.html)$/');

        $result = array();
        foreach ($regexIterator as $fileInfo) {
            $deprecates = $this->_findDeprecatesInFile($fileInfo);
            $result = array_merge($result, $deprecates);
        }

        return $result;
    }

    /**
     * Gathers all deprecated or removed stuff in a file
     *
     * @param SplFileInfo $fileInfo
     * @return array
     */
    protected function _findDeprecatesInFile($fileInfo)
    {
        $content = file_get_contents((string) $fileInfo);

        $result = array();
        $visitorMethods = $this->_getVisitorMethods();
        foreach ($visitorMethods as $method) {
            $deprecates = $this->$method($fileInfo, $content);
            $result = array_merge($result, $deprecates);
        }

        if (!$result) {
            return $result;
        }

        $filePath = substr($fileInfo, strlen(Mage::getRoot()) - 3); // 3 - length of 'app' prefix
        foreach ($result as $key => $value) {
            $result[$key] = 'Found deprecated and removed ' . $value['description'] . ' "' . $value['needle'] . '" in '
                . $filePath . ', suggested: ' . $value['suggestion'];
        }

        return $result;
    }

    /**
     * Finds usage of deprecated property skipCalculate
     *
     * @param SplFileInfo $fileInfo
     * @param string $content
     * @return array
     */
    protected function _visitSkipCalculate($fileInfo, $content)
    {
        // todo move to dev/tests/static/Legacy

        if (!$this->_fileHasExtensions($fileInfo, array('php', 'phtml', 'js'))) {
            return array();
        }

        if ($this->_fileHasExtensions($fileInfo, 'js')) {
            $needle = '.skipCalculate';
        } else {
            $needle = "'skipCalculate'";
        }

        $result = array();
        if (strpos($content, $needle) !== false) {
            $result = array(
                array(
                    'description' => 'deprecated configuration property',
                    'needle' => 'skipCalculate',
                    'suggestion' => 'remove it'
                )
            );
        }
        return $result;
    }
}
