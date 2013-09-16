<?php
/**
 * Abstract Google Experiment Tab
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
abstract class Magento_GoogleOptimizer_Block_Adminhtml_TabAbstract
    extends Magento_Backend_Block_Widget_Form implements Magento_Backend_Block_Widget_Tab_Interface
{
    /**
     * @var Magento_GoogleOptimizer_Helper_Data
     */
    protected $_helperData;

    /**
     * @var Magento_Core_Model_Registry
     */
    protected $_registry;

    /**
     * @var Magento_GoogleOptimizer_Helper_Code
     */
    protected $_codeHelper;

    /**
     * @var Magento_GoogleOptimizer_Helper_Form
     */
    protected $_formHelper;

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_GoogleOptimizer_Helper_Data $helperData
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_GoogleOptimizer_Helper_Code $codeHelper
     * @param Magento_GoogleOptimizer_Helper_Form $formHelper
     * @param Magento_Data_Form_Factory $formFactory
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        Magento_GoogleOptimizer_Helper_Data $helperData,
        Magento_Core_Model_Registry $registry,
        Magento_GoogleOptimizer_Helper_Code $codeHelper,
        Magento_GoogleOptimizer_Helper_Form $formHelper,
        Magento_Data_Form_Factory $formFactory,
        array $data = array()
    ) {
        parent::__construct($coreData, $context, $data);

        $this->_helperData = $helperData;
        $this->_registry = $registry;
        $this->_codeHelper = $codeHelper;
        $this->_formHelper = $formHelper;
        $this->setForm($formFactory->create());
    }

    /**
     * Prepare form before rendering HTML
     *
     * @return Magento_Backend_Block_Widget_Form
     */
    protected function _prepareForm()
    {
        $this->_formHelper->addGoogleoptimizerFields($this->getForm(), $this->_getGoogleExperiment());
        return parent::_prepareForm();
    }

    /**
     * Get google experiment code model
     *
     * @return Magento_GoogleOptimizer_Model_Code|null
     */
    protected function _getGoogleExperiment()
    {
        $entity = $this->_getEntity();
        if ($entity->getId()) {
            return $this->_codeHelper->getCodeObjectByEntity($entity);
        }
        return null;
    }

    /**
     * Get Entity model
     *
     * @return Magento_Catalog_Model_Abstract
     */
    protected abstract function _getEntity();

    /**
     * Can show tab in tabs
     *
     * @return boolean
     */
    public function canShowTab()
    {
        return $this->_helperData->isGoogleExperimentActive();
    }

    /**
     * Tab is hidden
     *
     * @return boolean
     */
    public function isHidden()
    {
        return false;
    }
}
