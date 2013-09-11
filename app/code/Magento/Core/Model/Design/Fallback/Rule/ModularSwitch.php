<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Fallback rule that delegates execution to either modular or non-modular sub-rule depending on input parameters
 */
namespace Magento\Core\Model\Design\Fallback\Rule;

class ModularSwitch
    implements \Magento\Core\Model\Design\Fallback\Rule\RuleInterface
{
    /**
     * @var \Magento\Core\Model\Design\Fallback\Rule\RuleInterface
     */
    private $_ruleNonModular;

    /**
     * @var \Magento\Core\Model\Design\Fallback\Rule\RuleInterface
     */
    private $_ruleModular;

    /**
     * Constructor
     *
     * @param \Magento\Core\Model\Design\Fallback\Rule\RuleInterface $ruleNonModular
     * @param \Magento\Core\Model\Design\Fallback\Rule\RuleInterface $ruleModular
     */
    public function __construct(
        \Magento\Core\Model\Design\Fallback\Rule\RuleInterface $ruleNonModular,
        \Magento\Core\Model\Design\Fallback\Rule\RuleInterface $ruleModular
    ) {
        $this->_ruleNonModular = $ruleNonModular;
        $this->_ruleModular = $ruleModular;
    }

    /**
     * Delegate execution to either modular or non-modular sub-rule depending on input parameters
     *
     * {@inheritdoc}
     * @throws \InvalidArgumentException
     */
    public function getPatternDirs(array $params)
    {
        $isNamespaceDefined = isset($params['namespace']);
        $isModuleDefined = isset($params['module']);
        if ($isNamespaceDefined && $isModuleDefined) {
            return $this->_ruleModular->getPatternDirs($params);
        } else if (!$isNamespaceDefined && !$isModuleDefined) {
            return $this->_ruleNonModular->getPatternDirs($params);
        }
        throw new \InvalidArgumentException("Parameters 'namespace' and 'module' should either be both set or unset.");
    }
}
