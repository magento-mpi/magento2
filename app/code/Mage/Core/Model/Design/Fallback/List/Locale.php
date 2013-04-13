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
class Mage_Core_Model_Design_Fallback_List_Locale implements Mage_Core_Model_Design_Fallback_Rule_RuleInterface
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
        $this->_rule = new Mage_Core_Model_Design_Fallback_Rule_Theme(
            new Mage_Core_Model_Design_Fallback_Rule_Simple("$themesDir/<area>/<theme_path>/locale/<locale>")
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
