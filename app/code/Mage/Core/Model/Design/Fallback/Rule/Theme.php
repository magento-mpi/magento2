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
 * Class with substitution parameters to values considering theme hierarchy
 */
class Mage_Core_Model_Design_Fallback_Rule_Theme implements Mage_Core_Model_Design_Fallback_Rule_RuleInterface
{
    /**
     * @var array of rules that should be iteratively applied for each theme in the row
     */
    protected $_rules;

    /**
     * Constructor
     *
     * @param array $rules
     * @throws InvalidArgumentException
     */
    public function __construct(array $rules)
    {
        foreach ($rules as $rule) {
            if (!($rule instanceof Mage_Core_Model_Design_Fallback_Rule_RuleInterface)) {
                throw new InvalidArgumentException(
                    'Each element should implement Mage_Core_Model_Design_Fallback_Rule_RuleInterface'
                );
            }
        }
        $this->_rules = $rules;
    }

    /**
     * Get ordered list of folders to search for a file
     *
     * @param array $params - array of parameters
     * @return array of folders to perform a search
     * @throws InvalidArgumentException
     */
    public function getPatternDirs(array $params)
    {
        $patternDirs = array();
        if (!array_key_exists('theme', $params) || !($params['theme'] instanceof Mage_Core_Model_ThemeInterface)) {
            throw new InvalidArgumentException(
                '$params["theme"] should be passed and should implement Mage_Core_Model_ThemeInterface'
            );
        }

        foreach ($this->_getThemeList($params['theme']) as $theme) {
            $params['theme_path'] = $theme->getThemePath();
            if ($params['theme_path']) {
                foreach ($this->_rules as $rule) {
                    $patternDirs = array_merge($patternDirs, $rule->getPatternDirs($params));
                }
            }
        }
        return $patternDirs;
    }

    /**
     * Get list of themes, which should be used for fallback. It's passed theme and all its parent themes
     *
     * @param Mage_Core_Model_ThemeInterface $theme
     * @return array
     */
    protected function _getThemeList(Mage_Core_Model_ThemeInterface $theme)
    {
        $result = array();
        $themeModel = $theme;
        while ($themeModel) {
            $result[] = $themeModel;
            $themeModel = $themeModel->getParentTheme();
        }
        return $result;
    }
}
