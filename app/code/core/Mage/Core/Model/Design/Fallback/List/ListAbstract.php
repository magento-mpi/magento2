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
 * Abstract class of fallback rules list for different types of files
 */
abstract class Mage_Core_Model_Design_Fallback_List_ListAbstract
    implements Mage_Core_Model_Design_Fallback_Rule_RuleInterface
{
    /**
     * @var array - ordered array of rules for concrete fallback
     */
    protected $_rules = array();

    /**
     * @var Mage_Core_Model_Dir
     */
    protected $_dir;

    /**
     * Constructor
     *
     * @param Mage_Core_Model_Dir $dir
     */
    public function __construct(Mage_Core_Model_Dir $dir)
    {
        $this->_dir = $dir;
        $this->_rules = $this->_setFallbackRules();
    }

    /**
     * Set rules in proper order for specific fallback procedure
     *
     * @return array of rules Mage_Core_Model_Design_Fallback_Rule_RuleInterface
     */
    abstract protected function _setFallbackRules();

    /**
     * Get ordered list of folders to search for a file
     *
     * @param string $fileName - relative file name
     * @param array $params - array of parameters
     * @param array $themeList - ordered array of themes - current theme and all its parents
     * @param bool $qualifiedDirsOnly - if false returns also pattern together with directory path
     * @return array of folders to perform a search
     */
    public function getPatternDirs($fileName, $params, $themeList, $qualifiedDirsOnly = true)
    {
        $dirs = array();
        foreach ($this->_rules as $rule) {
            $dirs = array_merge($dirs, $rule->getPatternDirs($fileName, $params, $themeList));
        }
        if ($qualifiedDirsOnly) {
            $return = array();
            foreach ($dirs as $dir) {
                $return[] = $dir['dir'];
            }
            return $return;
        } else {
            return $dirs;
        }
    }
}
