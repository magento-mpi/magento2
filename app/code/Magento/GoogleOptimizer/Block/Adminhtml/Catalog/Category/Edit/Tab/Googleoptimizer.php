<?php
/**
 * Google Optimizer Category Tab
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
namespace Magento\GoogleOptimizer\Block\Adminhtml\Catalog\Category\Edit\Tab;

class Googleoptimizer
    extends \Magento\Adminhtml\Block\Catalog\Form
{
    /**
     * @var \Magento\Core\Model\Registry
     */
    protected $_registry;

    /**
     * @var \Magento\GoogleOptimizer\Helper\Code
     */
    protected $_codeHelper;

    /**
     * @var \Magento\GoogleOptimizer\Helper\Form
     */
    protected $_formHelper;

    /**.
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Data_Form_Factory $formFactory
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_GoogleOptimizer_Helper_Code $codeHelper
     * @param Magento_GoogleOptimizer_Helper_Form $formHelper
     * @param Magento_Data_Form $form
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Registry $registry,
        Magento_Data_Form_Factory $formFactory,
        Magento_Core_Helper_Data $coreData,
        \Magento\Core\Model\Registry $registry,
        \Magento\GoogleOptimizer\Helper\Code $codeHelper,
        \Magento\GoogleOptimizer\Helper\Form $formHelper,
        \Magento\Data\Form $form,
        array $data = array()
    ) {
        parent::__construct($registry, $formFactory, $coreData, $context, $data);

        $this->_codeHelper = $codeHelper;
        $this->_formHelper = $formHelper;
        $this->setForm($form);
    }

    /**
     * Prepare form before rendering HTML
     *
     * @return \Magento\Backend\Block\Widget\Form
     */
    protected function _prepareForm()
    {
        $this->_formHelper->addGoogleoptimizerFields($this->getForm(), $this->_getGoogleExperiment());
        return parent::_prepareForm();
    }

    /**
     * Get google experiment code model
     *
     * @return \Magento\GoogleOptimizer\Model\Code|null
     * @throws \RuntimeException
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
     * @throws \RuntimeException
     */
    protected function _getCategory()
    {
        $entity = $this->_coreRegistry->registry('current_category');
        if (!$entity) {
            throw new \RuntimeException('Entity is not found in registry.');
        }
        return $entity;
    }
}
