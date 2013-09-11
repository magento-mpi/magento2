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
namespace Magento\Core\Model\Design\Fallback\Rule;

class Composite implements \Magento\Core\Model\Design\Fallback\Rule\RuleInterface
{
    /**
     * @var \Magento\Core\Model\Design\Fallback\Rule\RuleInterface[]
     */
    private $_rules = array();

    /**
     * Constructor
     *
     * @param \Magento\Core\Model\Design\Fallback\Rule\RuleInterface[] $rules
     * @throws \InvalidArgumentException
     */
    public function __construct(array $rules)
    {
        foreach ($rules as $rule) {
            if (!($rule instanceof \Magento\Core\Model\Design\Fallback\Rule\RuleInterface)) {
                throw new \InvalidArgumentException('Each item should implement the fallback rule interface.');
            }
        }
        $this->_rules = $rules;
    }

    /**
     * Retrieve sequentially combined directory patterns from child fallback rules
     *
     * {@inheritdoc}
     */
    public function getPatternDirs(array $params)
    {
        $result = array();
        foreach ($this->_rules as $rule) {
            $result = array_merge($result, $rule->getPatternDirs($params));
        }
        return $result;
    }
}
