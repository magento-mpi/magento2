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
class Mage_Core_Model_Design_Fallback_Rule_ModularSwitch implements Mage_Core_Model_Design_Fallback_Rule_RuleInterface
{
    /**
     * @var Mage_Core_Model_Design_Fallback_Rule_RuleInterface
     */
    private $_ruleNonModular;

    /**
     * @var Mage_Core_Model_Design_Fallback_Rule_RuleInterface
     */
    private $_ruleModular;

    /**
     * Constructor
     *
     * @param Mage_Core_Model_Design_Fallback_Rule_RuleInterface $ruleNonModular
     * @param Mage_Core_Model_Design_Fallback_Rule_RuleInterface $ruleModular
     */
    public function __construct(
        Mage_Core_Model_Design_Fallback_Rule_RuleInterface $ruleNonModular,
        Mage_Core_Model_Design_Fallback_Rule_RuleInterface $ruleModular
    ) {
        $this->_ruleNonModular = $ruleNonModular;
        $this->_ruleModular = $ruleModular;
    }

    /**
     * Delegate execution to either modular or non-modular sub-rule depending on input parameters
     *
     * {@inheritdoc}
     * @throws InvalidArgumentException
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
        throw new InvalidArgumentException("Parameters 'namespace' and 'module' should either be both set or unset.");
    }
}
