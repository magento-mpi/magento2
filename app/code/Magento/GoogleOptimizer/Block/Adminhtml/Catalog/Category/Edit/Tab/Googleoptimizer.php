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
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Data\Form\Factory $formFactory
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Backend\Block\Template\Context $context
     * @param Magento_GoogleOptimizer_Helper_Code $codeHelper
     * @param Magento_GoogleOptimizer_Helper_Form $formHelper
     * @param \Magento\Data\Form $form
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Model\Registry $registry,
        \Magento\Data\Form\Factory $formFactory,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Backend\Block\Template\Context $context,
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
