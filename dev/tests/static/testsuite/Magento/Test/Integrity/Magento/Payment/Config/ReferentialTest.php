<?php
/**
 * Validates that payment groups referenced from store configuration matches the groups declared in payment.xml
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Test_Integrity_Magento_Payment_Config_ReferentialTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var string[] $usedGroups all payment groups used in store configuration
     */
    protected static $_usedGroups = array();

    /** @var string[] $_registeredGroups all registered payment groups */
    protected static $_registeredGroups = array();


    public static function setUpBeforeClass()
    {
        self::_populateUsedGroups();
        self::_populateRegisteredGroups();
    }

    /**
     * Gathers all payment groups used in store configuration
     */
    private static function _populateUsedGroups()
    {
        /**
         * @var string[] $configFiles
         */
        $configFiles = Magento_TestFramework_Utility_Files::init()->getConfigFiles('config.xml', array(), false);
        /**
         * @var string $file
         */
        foreach ($configFiles as $file) {
            /**
             * @var DOMDocument $dom
             */
            $dom = new DOMDocument();
            $dom->loadXML(file_get_contents($file));

            $xpath = new DOMXPath($dom);
            foreach ($xpath->query('/config/*/payment/*/group') as $group) {
                if (!in_array($group->nodeValue, self::$_usedGroups)) {
                    self::$_usedGroups[] = $group->nodeValue;
                }
            }
        }
    }

    /**
     * Gathers all registered payment groups
     */
    private static function _populateRegisteredGroups()
    {
        /**
         * @var string[] $configFiles
         */
        $configFiles = Magento_TestFramework_Utility_Files::init()->getConfigFiles('payment.xml', array(), false);
        /**
         * @var string $file
         */
        foreach ($configFiles as $file) {
            /**
             * @var DOMDocument $dom
             */
            $dom = new DOMDocument();
            $dom->loadXML(file_get_contents($file));

            $xpath = new DOMXPath($dom);
            foreach ($xpath->query('/payment/groups/group') as $group) {
                $id = $group->attributes->getNamedItem('id')->nodeValue;
                if (!in_array($id, self::$_registeredGroups)) {
                    self::$_registeredGroups[] = $id;
                }
            }
        }
    }

    public function testGroupsExists()
    {
        $missing = array_diff(self::$_registeredGroups, self::$_usedGroups);

        if (!empty($missing)) {
            $message = sprintf(
                "The groups, referenced in store configuration for the payment, " .
                "don't correspond to any payment group declared in payment.xml: %s",
                implode(', ', $missing)
            );
            $this->fail($message);
        }
    }
}