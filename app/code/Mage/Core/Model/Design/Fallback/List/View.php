<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Fallback rules list for static view files
 */
class Mage_Core_Model_Design_Fallback_List_View implements Mage_Core_Model_Design_Fallback_Rule_RuleInterface
{
    /**
     * @var Mage_Core_Model_Design_Fallback_Rule_RuleInterface
     */
    private $_rule;

    /**
     * Constructor
     *
     * @param Mage_Core_Model_Dir $dir
     */
    public function __construct(Mage_Core_Model_Dir $dir)
    {
        $themesDir = $dir->getDir(Mage_Core_Model_Dir::THEMES);
        $modulesDir = $dir->getDir(Mage_Core_Model_Dir::MODULES);

        $this->_rule = new Mage_Core_Model_Design_Fallback_Rule_ModularSwitch(
            new Mage_Core_Model_Design_Fallback_Rule_Composite(array(
                new Mage_Core_Model_Design_Fallback_Rule_Theme(
                    new Mage_Core_Model_Design_Fallback_Rule_Composite(array(
                        new Mage_Core_Model_Design_Fallback_Rule_Simple(
                            "$themesDir/<area>/<theme_path>/locale/<locale>", array('locale')
                        ),
                        new Mage_Core_Model_Design_Fallback_Rule_Simple(
                            "$themesDir/<area>/<theme_path>"
                        ),
                    ))
                ),
                new Mage_Core_Model_Design_Fallback_Rule_Simple($dir->getDir(Mage_Core_Model_Dir::PUB_LIB)),
            )),
            new Mage_Core_Model_Design_Fallback_Rule_Composite(array(
                new Mage_Core_Model_Design_Fallback_Rule_Theme(
                    new Mage_Core_Model_Design_Fallback_Rule_Composite(array(
                        new Mage_Core_Model_Design_Fallback_Rule_Simple(
                            "$themesDir/<area>/<theme_path>/locale/<locale>/<namespace>_<module>", array('locale')
                        ),
                        new Mage_Core_Model_Design_Fallback_Rule_Simple(
                            "$themesDir/<area>/<theme_path>/<namespace>_<module>"
                        ),
                    ))
                ),
                new Mage_Core_Model_Design_Fallback_Rule_Simple(
                    "$modulesDir/<namespace>/<module>/view/<area>/locale/<locale>", array('locale')
                ),
                new Mage_Core_Model_Design_Fallback_Rule_Simple(
                    "$modulesDir/<namespace>/<module>/view/<area>"
                ),
            ))
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getPatternDirs(array $params)
    {
        return $this->_rule->getPatternDirs($params);
    }
}
