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
class Magento_Core_Model_Design_Fallback_Rule_Composite implements Magento_Core_Model_Design_Fallback_Rule_RuleInterface
{
    /**
     * @var Magento_Core_Model_Design_Fallback_Rule_RuleInterface[]
     */
    private $_rules = array();

    /**
     * Constructor
     *
     * @param Magento_Core_Model_Design_Fallback_Rule_RuleInterface[] $rules
     * @throws InvalidArgumentException
     */
    public function __construct(array $rules)
    {
        foreach ($rules as $rule) {
            if (!($rule instanceof Magento_Core_Model_Design_Fallback_Rule_RuleInterface)) {
                throw new InvalidArgumentException('Each item should implement the fallback rule interface.');
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
