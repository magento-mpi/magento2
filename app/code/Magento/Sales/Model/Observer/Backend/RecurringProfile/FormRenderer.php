<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Model\Observer\Backend\RecurringProfile;

class FormRenderer
{
    /**
     * @var \Magento\Core\Model\BlockFactory
     */
    protected $_blockFactory;

    /**
     * @param \Magento\Core\Model\BlockFactory $blockFactory
     */
    public function __construct(\Magento\Core\Model\BlockFactory $blockFactory)
    {
        $this->_blockFactory = $blockFactory;
    }

    /**
     * Add the recurring profile form when editing a product
     *
     * @param \Magento\Event\Observer $observer
     */
    public function render($observer)
    {
        // replace the element of recurring payment profile field with a form
        $profileElement = $observer->getEvent()->getProductElement();
        $product = $observer->getEvent()->getProduct();

        /** @var $formBlock \Magento\Sales\Block\Adminhtml\Recurring\Profile\Edit\Form */
        $formBlock = $this->_blockFactory->createBlock('\Magento\Sales\Block\Adminhtml\Recurring\Profile\Edit\Form');
        $formBlock->setNameInLayout('adminhtml_recurring_profile_edit_form');
        $formBlock->setParentElement($profileElement);
        $formBlock->setProductEntity($product);
        $output = $formBlock->toHtml();

        // make the profile element dependent on is_recurring
        /** @var $dependencies \Magento\Backend\Block\Widget\Form\Element\Dependence */
        $dependencies = $this->_blockFactory->createBlock('\Magento\Backend\Block\Widget\Form\Element\Dependence');
        $dependencies->setNameInLayout('adminhtml_recurring_profile_edit_form_dependence');
        $dependencies->addFieldMap('is_recurring', 'product[is_recurring]');
        $dependencies->addFieldMap($profileElement->getHtmlId(), $profileElement->getName());
        $dependencies->addFieldDependence($profileElement->getName(), 'product[is_recurring]', '1');
        $dependencies->addConfigOptions(array('levels_up' => 2));

        $output .= $dependencies->toHtml();

        $observer->getEvent()->getResult()->output = $output;
    }
}
