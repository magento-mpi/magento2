<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Fallback rules list for locale files
 */
class Mage_Core_Model_Design_Fallback_List_Locale extends Mage_Core_Model_Design_Fallback_List_ListAbstract
{
    /**
     * Set rules in proper order for specific fallback procedure
     *
     * @return array of rules Mage_Core_Model_Design_Fallback_Rule_RuleInterface
     */
    protected function _setFallbackRules()
    {
        return array(
            new Mage_Core_Model_Design_Fallback_Rule_Theme(array(
                $this->_dir->getDir(Mage_Core_Model_Dir::THEMES) . '/<area>/<theme_path>/locale/<locale>',
            ))
        );
    }
}
