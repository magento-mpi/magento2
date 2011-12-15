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

/**
* Tests to find Enterprise Invitation Helper obsolete methods still used
*/
class Legacy_Enterprise_Invitation_Helper_DataTest extends PHPUnit_Framework_TestCase
{
    /**
     * @param string $file
     * @dataProvider obsoleteMethodsDataProvider
     */
    public function testObsoleteMethods($file)
    {
        $configModel = "Mage::getSingleton('Enterprise_Invitation_Model_Config')";
        $obsoleteMethods = array(
            'getMaxInvitationsPerSend'   => "{$configModel}->getMaxInvitationsPerSend()",
            'getInvitationRequired'      => "{$configModel}->getInvitationRequired()",
            'getUseInviterGroup'         => "{$configModel}->getUseInviterGroup()",
            'isInvitationMessageAllowed' => "{$configModel}->isInvitationMessageAllowed()",
            'isEnabled'                  => "{$configModel}->isEnabled()",
        );
        $content = file_get_contents($file);
        foreach ($obsoleteMethods as $method => $suggestion) {
            $suggestion = sprintf(Legacy_ObsoleteCodeTest::SUGGESTION_MESSAGE, $suggestion);
            $this->assertNotRegExp(
                '/Enterprise_Invitation_Helper_Data[^;(]+?' . preg_quote($method, '/') . '\s*\(/i',
                $content,
                "Method 'Enterprise_Invitation_Helper_Data::$method' is obsolete. $suggestion"
            );
        }
    }

    /**
     * @return array
     */
    public function obsoleteMethodsDataProvider()
    {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator(PATH_TO_SOURCE_CODE . '/app/code/core/Enterprise/Invitation')
        );
        $regexIterator = new RegexIterator($iterator, '/\.(?:php|phtml)$/');
        $files = array();
        foreach ($regexIterator as $fileInfo) {
            $file = (string)$fileInfo;
            /* Exclude files that don't need to be validated */
            $content = file_get_contents($file);
            if (strpos($content, 'Enterprise_Invitation_Helper_Data') === false) {
                continue;
            }
            $files[] = $file;
        }
        return FileDataProvider::composeDataSets($files);
    }
}
