<?php
/**
 * Google Optimizer Category Tab
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
class Magento_GoogleOptimizer_Block_Adminhtml_Catalog_Category_Edit_Tab_Googleoptimizer
    extends Magento_Adminhtml_Block_Catalog_Form
{
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
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_Store_Config $coreStoreConfig
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_GoogleOptimizer_Helper_Code $codeHelper
     * @param Magento_GoogleOptimizer_Helper_Form $formHelper
     * @param Magento_Data_Form $form
     * @param array $data
     */
    public function __construct(
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_Store_Config $coreStoreConfig,
        Magento_Core_Model_Registry $registry,
        Magento_GoogleOptimizer_Helper_Code $codeHelper,
        Magento_GoogleOptimizer_Helper_Form $formHelper,
        Magento_Data_Form $form,
        array $data = array()
    ) {
        parent::__construct($context, $coreStoreConfig, $data);

        $this->_codeHelper = $codeHelper;
        $this->_formHelper = $formHelper;
        $this->_registry = $registry;
        $this->setForm($form);
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
     * @throws RuntimeException
     */
    protected function _getGoogleExperiment()
    {
        $category = $this->_getCategory();
        if ($category->getId()) {
            return $this->_codeHelper->getCodeObjectByEntity($category);
        }
        return null;
    }

    /**
     * Get category model from registry
     *
     * @return mixed
     * @throws RuntimeException
     */
    protected function _getCategory()
    {
        $entity = $this->_registry->registry('current_category');
        if (!$entity) {
            throw new RuntimeException('Entity is not found in registry.');
        }
        return $entity;
    }
}
