<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\RecurringPayment\Block\Adminhtml\Product\Edit\Tab\Price;

/**
 * Recurring payment attribute edit renderer
 */
class Recurring extends \Magento\Catalog\Block\Adminhtml\Form\Renderer\Fieldset\Element
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Framework\View\Element\BlockFactory
     */
    protected $_blockFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\View\Element\BlockFactory $blockFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\View\Element\BlockFactory $blockFactory,
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

        /** @var $formBlock \Magento\RecurringPayment\Block\Adminhtml\Payment\Edit\Form */
        $formBlock = $this->_blockFactory->createBlock('Magento\RecurringPayment\Block\Adminhtml\Payment\Edit\Form');
        $formBlock->setNameInLayout('adminhtml_recurring_payment_edit_form');
        $formBlock->setParentElement($this->_element);
        $formBlock->setProductEntity($product);
        $output = $formBlock->toHtml();

        // make the payment element dependent on is_recurring
        /** @var $dependencies \Magento\Backend\Block\Widget\Form\Element\Dependence */
        $dependencies = $this->_blockFactory->createBlock('Magento\Backend\Block\Widget\Form\Element\Dependence');
        $dependencies->setNameInLayout('adminhtml_recurring_payment_edit_form_dependence');
        $dependencies->addFieldMap('is_recurring', 'product[is_recurring]');
        $dependencies->addFieldMap($this->_element->getHtmlId(), $this->_element->getName());
        $dependencies->addFieldDependence($this->_element->getName(), 'product[is_recurring]', '1');
        $dependencies->addConfigOptions(array('levels_up' => 2));

        $output .= $dependencies->toHtml();

        return $output;
    }
}
