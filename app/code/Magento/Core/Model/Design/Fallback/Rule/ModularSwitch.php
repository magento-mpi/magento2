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
class Magento_Core_Model_Design_Fallback_Rule_ModularSwitch
    implements Magento_Core_Model_Design_Fallback_Rule_RuleInterface
{
    /**
     * @var Magento_Core_Model_Design_Fallback_Rule_RuleInterface
     */
    private $_ruleNonModular;

    /**
     * @var Magento_Core_Model_Design_Fallback_Rule_RuleInterface
     */
    private $_ruleModular;

    /**
     * Constructor
     *
     * @param Magento_Core_Model_Design_Fallback_Rule_RuleInterface $ruleNonModular
     * @param Magento_Core_Model_Design_Fallback_Rule_RuleInterface $ruleModular
     */
    public function __construct(
        Magento_Core_Model_Design_Fallback_Rule_RuleInterface $ruleNonModular,
        Magento_Core_Model_Design_Fallback_Rule_RuleInterface $ruleModular
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
