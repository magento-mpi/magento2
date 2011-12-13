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

class Legacy_Enterprise_Invitation_Helper_DataTest extends PHPUnit_Framework_TestCase
{
    /**
     * @param string $file
     * @dataProvider deprecatedMethodsDataProvider
     */
    public function testDeprecatedMethods($file)
    {
        $configModel = "Mage::getSingleton('Enterprise_Invitation_Model_Config')";
        $deprecations = array(
            'getMaxInvitationsPerSend'   => "use {$configModel}->getMaxInvitationsPerSend() instead",
            'getInvitationRequired'      => "use {$configModel}->getInvitationRequired() instead",
            'getUseInviterGroup'         => "use {$configModel}->getUseInviterGroup() instead",
            'isInvitationMessageAllowed' => "use {$configModel}->isInvitationMessageAllowed() instead",
            'isEnabled'                  => "use {$configModel}->isEnabled() instead",
        );
        $content = file_get_contents($file);
        foreach ($deprecations as $method => $suggestion) {
            $this->assertNotRegExp(
                '/Enterprise_Invitation_Helper_Data[^;(]+?' . preg_quote($method, '/') . '\s*\(/i',
                $content,
                "Deprecated method 'Enterprise_Invitation_Helper_Data::$method' is used, $suggestion."
            );
        }
    }

    public function deprecatedMethodsDataProvider()
    {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator(PATH_TO_SOURCE_CODE . '/app/code/core/Enterprise/Invitation')
        );
        $regexIterator = new RegexIterator($iterator, '/\.(?:php|phtml)$/');
        $result = array();
        foreach ($regexIterator as $fileInfo) {
            $file = (string)$fileInfo;
            /* Exclude files that don't need to be validated */
            $content = file_get_contents($file);
            if (strpos($content, 'Enterprise_Invitation_Helper_Data') === false) {
                continue;
            }
            /* Use filename as a data set name to not include it to every assertion message */
            $result[$file] = array($file);
        }
        return $result;
    }
}
