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
 * Fallback rules list for static view files
 */
class Mage_Core_Model_Design_Fallback_List_View extends Mage_Core_Model_Design_Fallback_List_ListAbstract
{
    /**
     * Set rules in proper order for specific fallback procedure
     *
     * @return array of rules Mage_Core_Model_Design_Fallback_Rule_RuleInterface
     */
    protected function _getFallbackRules()
    {
        $return = array();
        $themeDir = $this->_dir->getDir(Mage_Core_Model_Dir::THEMES);
        $return[] = new Mage_Core_Model_Design_Fallback_Rule_Theme(
            array(
                new Mage_Core_Model_Design_Fallback_Rule_Simple(
                    $themeDir . '/<area>/<theme_path>/locale/<locale>',
                    array('locale')
                ),
                new Mage_Core_Model_Design_Fallback_Rule_Simple(
                    $themeDir . '/<area>/<theme_path>'
                ),
                new Mage_Core_Model_Design_Fallback_Rule_Simple(
                    $themeDir . '/<area>/<theme_path>/locale/<locale>/<namespace>_<module>',
                    array('namespace', 'module', 'locale')
                ),
                new Mage_Core_Model_Design_Fallback_Rule_Simple(
                    $themeDir . '/<area>/<theme_path>/<namespace>_<module>',
                    array('namespace', 'module')
                )
            )
        );

        $moduleDir = $this->_dir->getDir(Mage_Core_Model_Dir::MODULES);
        $return[] = new Mage_Core_Model_Design_Fallback_Rule_Simple(
            $moduleDir . '/<namespace>/<module>/view/<area>/locale/<locale>',
            array('namespace', 'module', 'locale')
        );
        $return[] = new Mage_Core_Model_Design_Fallback_Rule_Simple(
            $moduleDir . '/<namespace>/<module>/view/<area>',
            array('namespace', 'module')
        );

        $return[] = new Mage_Core_Model_Design_Fallback_Rule_Simple(
            $this->_dir->getDir(Mage_Core_Model_Dir::PUB_LIB)
        );
        return $return;
    }
}
