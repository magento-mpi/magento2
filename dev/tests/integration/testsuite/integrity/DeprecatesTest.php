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
            $result[$key] = 'Found deprecated and removed ' . $value['description'] . ' "' . $value['needle'] . '" in '
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
                'description' => 'method',
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
                'description' => 'method',
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
                'description' => 'method',
                'needle' => $needle . ')',
                'suggestion' => 'use getTrackingPopupUrlBySalesModel()'
            );
        }

        return $result;
    }

    /**
     * Finds usage of deprecated methods that duplicates invitation config model
     *
     * @param SplFileInfo $fileInfo
     * @param string $content
     * @return array
     */
    protected function _visitInvitationHelperMethods($fileInfo, $content)
    {
        if (!$this->_fileHasExtensions($fileInfo, array('php', 'phtml'))) {
            return array();
        }

        // Optimization to not use regexps where not needed
        if (strpos($content, 'Enterprise_Invitation_Helper_Data') === false) {
            return array();
        }

        $methods = array(
            'getMaxInvitationsPerSend',
            'getInvitationRequired',
            'getUseInviterGroup',
            'isInvitationMessageAllowed',
            'isEnabled'
        );

        $result = array();
        foreach ($methods as $method) {
            $pattern = '/Enterprise_Invitation_Helper_Data[^;(]+' . $method . '\(/';
            if (!preg_match($pattern, $content)) {
                continue;
            }
            $result[] = array(
                'description' => 'invitation helper method',
                'needle' => $method . '()',
                'suggestion' => "use Mage::getSingleton('Enterprise_Invitation_Model_Config')->{$method}()"
            );
        }

        return $result;
    }
}
