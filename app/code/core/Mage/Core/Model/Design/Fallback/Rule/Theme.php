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
     * Constructor
     *
     * @param array $patternsArray
     * @throws InvalidArgumentException
     */
    public function __construct(array $patternsArray)
    {
        foreach ($patternsArray as $pattern) {
            if (strpos($pattern, '<theme_path>') === false) {
                throw new InvalidArgumentException("Pattern must contain '<theme_path>' node");
            }
        }
        $this->_patternsArray = $patternsArray;
    }

    /**
     * Get ordered list of folders to search for a file
     *
     * @param string $fileName - relative file name
     * @param array $params - array of parameters
     * @param array $themeList - ordered array of themes - current theme and all its parents
     * @return array of folders to perform a search
     */
    public function getPatternDirs($fileName, $params, $themeList)
    {
        $patterns = array();

        foreach ($themeList as $theme) {
            $params['theme_path'] = $theme->getThemePath();
            if ($params['theme_path']) {
                foreach ($this->_patternsArray as $pattern) {
                    $simpleRule = new Mage_Core_Model_Design_Fallback_Rule_Simple($pattern);
                    $patterns = array_merge($patterns, $simpleRule->getPatternDirs($fileName, $params, $themeList));
                }
            }
        }
        return $patterns;
    }
}
