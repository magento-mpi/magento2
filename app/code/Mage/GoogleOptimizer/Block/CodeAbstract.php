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
     * @param Mage_Core_Block_Template_Context $context
     * @param Mage_GoogleOptimizer_Helper_Data $helper
     * @param Mage_Core_Model_Registry $registry
     * @param array $data
     */
    public function __construct(
        Mage_Core_Block_Template_Context $context,
        Mage_GoogleOptimizer_Helper_Data $helper,
        Mage_Core_Model_Registry $registry,
        array $data = array()
    ) {
        $this->_helper = $helper;
        $this->_registry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * Get google experiment code model
     *
     * @return Mage_GoogleOptimizer_Model_Code
     */
    protected function _getGoogleExperimentModel()
    {
        return $this->_getEntity()->getGoogleExperiment();
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
     * Return script
     *
     * @return string
     */
    protected function _getScriptCode()
    {
        $result = '';

        if ($this->_helper->isGoogleExperimentActive() && $this->_getGoogleExperimentModel()) {
            $result = $this->_getGoogleExperimentModel()->getData('experiment_script');
        }
        return $result;
    }

    /**
     * @return Mage_Core_Model_Abstract
     */
    protected function _getEntity()
    {
        return $this->_registry->registry($this->_registryName);
    }
}
