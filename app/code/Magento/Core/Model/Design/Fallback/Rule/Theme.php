<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * An aggregate of a fallback rule that propagates it to every theme according to a hierarchy
 */
class Magento_Core_Model_Design_Fallback_Rule_Theme implements Magento_Core_Model_Design_Fallback_Rule_RuleInterface
{
    /**
     * @var Magento_Core_Model_Design_Fallback_Rule_RuleInterface
     */
    private $_rule;

    /**
     * Constructor
     *
     * @param Magento_Core_Model_Design_Fallback_Rule_RuleInterface $rule
     */
    public function __construct(Magento_Core_Model_Design_Fallback_Rule_RuleInterface $rule)
    {
        $this->_rule = $rule;
    }

    /**
     * Propagate an underlying fallback rule to every theme in a hierarchy: parent, grandparent, etc.
     *
     * {@inheritdoc}
     * @throws InvalidArgumentException
     */
    public function getPatternDirs(array $params)
    {
        if (!array_key_exists('theme', $params) || !($params['theme'] instanceof Magento_Core_Model_ThemeInterface)) {
            throw new InvalidArgumentException(
                'Parameter "theme" should be specified and should implement the theme interface.'
            );
        }
        $result = array();
        /** @var $theme Magento_Core_Model_ThemeInterface */
        $theme = $params['theme'];
        unset($params['theme']);
        while ($theme) {
            if ($theme->getThemePath()) {
                $params['theme_path'] = $theme->getThemePath();
                $result = array_merge($result, $this->_rule->getPatternDirs($params));
            }
            $theme = $theme->getParentTheme();
        }
        return $result;
    }
}
