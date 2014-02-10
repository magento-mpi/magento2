<?php
/**
 * Product edit form observer
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\ConfigurableProduct\Block\Adminhtml\Product\Edit\Tab\Advanced;

use Magento\Backend\Model\Config\Source;

class Observer
{
    /**
     * @var \Magento\Backend\Model\Config\Source\Yesno
     */
    protected $optionList;

    /**
     * @param Source\Yesno $optionList
     */
    public function __construct(Source\Yesno $optionList)
    {
        $this->optionList = $optionList;
    }

    /**
     * @param \Magento\Event $event
     */
    public function observe($event)
    {
        /** @var \Magento\Data\Form\AbstractForm $form */
        $form = $event->getForm();
        /** @var  $fieldset */
        $fieldset = $form->getElement('advanced_fieldset');

        $fieldset->addField('is_configurable', 'select', array(
            'name' => 'is_configurable',
            'label' => __('Use To Create Configurable Product'),
            'values' => $this->optionList->toOptionArray()
        ));
    }
} 
