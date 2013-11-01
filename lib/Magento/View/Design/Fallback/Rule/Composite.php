<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Composite rule that represents sequence of child fallback rules
 */
namespace Magento\View\Design\Fallback\Rule;

/**
 * Composite Rules
 *
 * @package Magento\View
 */
class Composite implements RuleInterface
{
    /**
     * @var RuleInterface[]
     */
    protected $rules = array();

    /**
     * Constructor
     *
     * @param RuleInterface[] $rules
     * @throws \InvalidArgumentException
     */
    public function __construct(array $rules)
    {
        foreach ($rules as $rule) {
            if (!($rule instanceof RuleInterface)) {
                throw new \InvalidArgumentException('Each item should implement the fallback rule interface.');
            }
        }
        $this->rules = $rules;
    }

    /**
     * Retrieve sequentially combined directory patterns from child fallback rules
     *
     * {@inheritdoc}
     */
    public function getPatternDirs(array $params)
    {
        $result = array();
        foreach ($this->rules as $rule) {
            $result = array_merge($result, $rule->getPatternDirs($params));
        }
        return $result;
    }
}
