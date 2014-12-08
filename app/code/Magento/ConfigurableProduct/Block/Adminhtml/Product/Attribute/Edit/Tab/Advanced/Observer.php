<?php
/**
 * Product edit form observer
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\ConfigurableProduct\Block\Adminhtml\Product\Attribute\Edit\Tab\Advanced;

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
     * @param \Magento\Framework\Event $event
     * @return void
     */
    public function observe($event)
    {
        /** @var \Magento\Framework\Data\Form\AbstractForm $form */
        $form = $event->getForm();
        /** @var  $fieldset */
        $fieldset = $form->getElement('advanced_fieldset');

        $fieldset->addField(
            'is_configurable',
            'select',
            [
                'name' => 'is_configurable',
                'label' => __('Use To Create Configurable Product'),
                'values' => $this->optionList->toOptionArray()
            ]
        );
    }
}
