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
namespace Magento\View\Design\Fallback\Rule;

use Magento\View\Design\ThemeInterface;

/**
 * Theme
 *
 * @package Magento\View
 */
class Theme implements RuleInterface
{
    /**
     * @var RuleInterface
     */
    protected $rule;

    /**
     * Constructor
     *
     * @param RuleInterface $rule
     */
    public function __construct(RuleInterface $rule)
    {
        $this->rule = $rule;
    }

    /**
     * Propagate an underlying fallback rule to every theme in a hierarchy: parent, grandparent, etc.
     *
     * {@inheritdoc}
     * @throws \InvalidArgumentException
     */
    public function getPatternDirs(array $params)
    {
        if (!array_key_exists('theme', $params) || !($params['theme'] instanceof ThemeInterface)) {
            throw new \InvalidArgumentException(
                'Parameter "theme" should be specified and should implement the theme interface.'
            );
        }
        $result = array();
        /** @var $theme ThemeInterface */
        $theme = $params['theme'];
        unset($params['theme']);
        while ($theme) {
            if ($theme->getThemePath()) {
                $params['theme_path'] = $theme->getThemePath();
                $result = array_merge($result, $this->rule->getPatternDirs($params));
            }
            $theme = $theme->getParentTheme();
        }
        return $result;
    }
}
