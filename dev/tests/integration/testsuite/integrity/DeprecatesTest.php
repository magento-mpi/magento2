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
        $regexIterator = new RegexIterator($iterator, '/(\.php|\.phtml|\.xml)$/');

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
            $result[$key] = 'Deprecated ' . $value['description'] . ' "' . $value['needle'] . '" is found in '
                . $filePath . ', suggested: ' . $value['suggestion'];
        }

        return $result;
    }

    /**
     * Finds usage of htmlEscape method
     *
     * @param SplFileInfo $fileInfo
     * @param string $content
     * @return array
     */
    protected function _visitHtmlEscape($fileInfo, $content)
    {
        if (!$this->_fileHasExtensions($fileInfo, array('php', 'phtml'))) {
            return array();
        }

        $result = array();
        if (strpos($content, 'htmlEscape(') !== false) {
            $result[] = array(
                'description' => 'removed method',
                'needle' => 'htmlEscape()',
                'suggestion' => 'change to escapeHtml()'
            );
        }

        return $result;
    }

    /**
     * Finds usage of urlEscape method
     *
     * @param SplFileInfo $fileInfo
     * @param string $content
     * @return array
     */
    protected function _visitUrlEscape($fileInfo, $content)
    {
        if (!$this->_fileHasExtensions($fileInfo, array('php', 'phtml'))) {
            return array();
        }

        $result = array();
        if (strpos($content, 'urlEscape(') !== false) {
            $result[] = array(
                'description' => 'removed method',
                'needle' => 'urlEscape()',
                'suggestion' => 'change to escapeUrl()'
            );
        }

        return $result;
    }

    /**
     * Finds usage of deprecated methods that compose shipping tracking urls
     *
     * @param SplFileInfo $fileInfo
     * @param string $content
     * @return array
     */
    protected function _visitTrackingPopUpUrl($fileInfo, $content)
    {
        if (!$this->_fileHasExtensions($fileInfo, array('php', 'phtml'))) {
            return array();
        }

        $search = array(
            'getTrackingPopUpUrlByOrderId(',
            'getTrackingPopUpUrlByShipId(',
            'getTrackingPopUpUrlByTrackId('
        );

        $result = array();
        foreach ($search as $needle) {
            if (strpos($content, $needle) === false) {
                continue;
            }
            $result[] = array(
                'description' => 'removed method',
                'needle' => $needle . ')',
                'suggestion' => 'use getTrackingPopupUrlBySalesModel()'
            );
        }

        return $result;
    }
}
