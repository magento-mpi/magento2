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
     * @var Mage_Core_Model_Design_Fallback_Rule_RuleInterface[]
     */
    protected $_rules;

    /**
     * Constructor
     *
     * @param array $rules Rules to be propagated to every theme involved into inheritance
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
        if (!array_key_exists('theme', $params) || !($params['theme'] instanceof Mage_Core_Model_ThemeInterface)) {
            throw new InvalidArgumentException(
                '$params["theme"] should be passed and should implement Mage_Core_Model_ThemeInterface'
            );
        }
        $result = array();
        /** @var $theme Mage_Core_Model_ThemeInterface */
        $theme = $params['theme'];
        while ($theme) {
            if ($theme->getThemePath()) {
                $params['theme_path'] = $theme->getThemePath();
                foreach ($this->_rules as $rule) {
                    $result = array_merge($result, $rule->getPatternDirs($params));
                }
            }
            $theme = $theme->getParentTheme();
        }
        return $result;
    }
}
