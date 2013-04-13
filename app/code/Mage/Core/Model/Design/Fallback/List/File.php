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
 * Fallback rules list for non-public files
 */
class Mage_Core_Model_Design_Fallback_List_File implements Mage_Core_Model_Design_Fallback_Rule_RuleInterface
{
    /**
     * @var Mage_Core_Model_Design_Fallback_Rule_RuleInterface
     */
    private $_ruleNonModular;

    /**
     * @var Mage_Core_Model_Design_Fallback_Rule_RuleInterface
     */
    private $_ruleModular;

    /**
     * Constructor
     *
     * @param Mage_Core_Model_Dir $dir
     */
    public function __construct(Mage_Core_Model_Dir $dir)
    {
        $themesDir = $dir->getDir(Mage_Core_Model_Dir::THEMES);
        $modulesDir = $dir->getDir(Mage_Core_Model_Dir::MODULES);

        $this->_ruleNonModular = new Mage_Core_Model_Design_Fallback_Rule_Theme(
            new Mage_Core_Model_Design_Fallback_Rule_Simple("$themesDir/<area>/<theme_path>")
        );

        $this->_ruleModular = new Mage_Core_Model_Design_Fallback_Rule_Composite(array(
            new Mage_Core_Model_Design_Fallback_Rule_Theme(
                new Mage_Core_Model_Design_Fallback_Rule_Simple("$themesDir/<area>/<theme_path>/<namespace>_<module>")
            ),
            new Mage_Core_Model_Design_Fallback_Rule_Simple("$modulesDir/<namespace>/<module>/view/<area>"),
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getPatternDirs(array $params)
    {
        $rule = isset($params['namespace']) || isset($params['module']) ? $this->_ruleModular : $this->_ruleNonModular;
        return $rule->getPatternDirs($params);
    }
}
