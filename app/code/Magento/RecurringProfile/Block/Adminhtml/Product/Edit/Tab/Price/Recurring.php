<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Recurring profile attribute edit renderer
 */
namespace Magento\RecurringProfile\Block\Adminhtml\Product\Edit\Tab\Price;

class Recurring
    extends \Magento\Catalog\Block\Adminhtml\Form\Renderer\Fieldset\Element
{
    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\View\Element\BlockFactory
     */
    protected $_blockFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\View\Element\BlockFactory $blockFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Model\Registry $registry,
        \Magento\View\Element\BlockFactory $blockFactory,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        $this->_blockFactory = $blockFactory;
        parent::__construct($context, $data);
    }

    /**
     * Element output getter
     *
     * @return string
     */
    public function getElementHtml()
    {
        $product = $this->_coreRegistry->registry('current_product');

        /** @var $formBlock \Magento\RecurringProfile\Block\Adminhtml\Profile\Edit\Form */
        $formBlock = $this->_blockFactory->createBlock('Magento\RecurringProfile\Block\Adminhtml\Profile\Edit\Form');
        $formBlock->setNameInLayout('adminhtml_recurring_profile_edit_form');
        $formBlock->setParentElement($this->_element);
        $formBlock->setProductEntity($product);
        $output = $formBlock->toHtml();

        // make the profile element dependent on is_recurring
        /** @var $dependencies \Magento\Backend\Block\Widget\Form\Element\Dependence */
        $dependencies = $this->_blockFactory->createBlock('Magento\Backend\Block\Widget\Form\Element\Dependence');
        $dependencies->setNameInLayout('adminhtml_recurring_profile_edit_form_dependence');
        $dependencies->addFieldMap('is_recurring', 'product[is_recurring]');
        $dependencies->addFieldMap($this->_element->getHtmlId(), $this->_element->getName());
        $dependencies->addFieldDependence($this->_element->getName(), 'product[is_recurring]', '1');
        $dependencies->addConfigOptions(array('levels_up' => 2));

        $output .= $dependencies->toHtml();

        return $output;
    }
}
