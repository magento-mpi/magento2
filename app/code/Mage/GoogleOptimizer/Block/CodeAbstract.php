<?php
/**
 * Google Optimizer Scripts Block
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
abstract class Mage_GoogleOptimizer_Block_CodeAbstract extends Mage_Core_Block_Template
{
    /**
     * @var Entity name in registry
     */
    protected $_registryName;

    /**
     * @var Mage_Core_Model_Registry
     */
    protected $_registry;

    /**
     * @var Mage_GoogleOptimizer_Helper_Data
     */
    protected $_helper;

    /**
     * @var Mage_GoogleOptimizer_Helper_Code
     */
    protected $_codeHelper;

    /**
     * @param Mage_Core_Block_Template_Context $context
     * @param Mage_GoogleOptimizer_Helper_Data $helper
     * @param Mage_Core_Model_Registry $registry
     * @param Mage_GoogleOptimizer_Helper_Code $codeHelper
     * @param array $data
     */
    public function __construct(
        Mage_Core_Block_Template_Context $context,
        Mage_GoogleOptimizer_Helper_Data $helper,
        Mage_Core_Model_Registry $registry,
        Mage_GoogleOptimizer_Helper_Code $codeHelper,
        array $data = array()
    ) {
        $this->_helper = $helper;
        $this->_registry = $registry;
        $this->_codeHelper = $codeHelper;
        parent::__construct($context, $data);
    }

    /**
     * Get google experiment code model
     *
     * @return Mage_GoogleOptimizer_Model_Code
     * @throws RuntimeException
     */
    protected function _getGoogleExperiment()
    {
        return $this->_codeHelper->getCodeObjectByEntity($this->_getEntity());
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        return parent::_toHtml() . $this->_getScriptCode();
    }

    /**
     * Return script code
     *
     * @return string
     */
    protected function _getScriptCode()
    {
        $result = '';

        if ($this->_helper->isGoogleExperimentActive() && $this->_getGoogleExperiment()) {
            $result = $this->_getGoogleExperiment()->getData('experiment_script');
        }
        return $result;
    }

    /**
     * Get entity from registry
     *
     * @return mixed
     * @throws RuntimeException
     */
    protected function _getEntity()
    {
        $entity = $this->_registry->registry($this->_registryName);
        if (!$entity) {
            throw new RuntimeException('Entity is not found in registry.');
        }
        return $entity;
    }
}