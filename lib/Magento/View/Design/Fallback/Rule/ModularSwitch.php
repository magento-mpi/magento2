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
namespace Magento\View\Design\Fallback\Rule;

/**
 * Modular Switch
 *
 * @package Magento\View
 */
class ModularSwitch implements RuleInterface
{
    /**
     * @var RuleInterface
     */
    protected $ruleNonModular;

    /**
     * @var RuleInterface
     */
    protected $ruleModular;

    /**
     * Constructor
     *
     * @param RuleInterface $ruleNonModular
     * @param RuleInterface $ruleModular
     */
    public function __construct(
        RuleInterface $ruleNonModular,
        RuleInterface $ruleModular
    ) {
        $this->ruleNonModular = $ruleNonModular;
        $this->ruleModular = $ruleModular;
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
            return $this->ruleModular->getPatternDirs($params);
        } elseif (!$isNamespaceDefined && !$isModuleDefined) {
            return $this->ruleNonModular->getPatternDirs($params);
        }
        throw new \InvalidArgumentException("Parameters 'namespace' and 'module' should either be both set or unset.");
    }
}
