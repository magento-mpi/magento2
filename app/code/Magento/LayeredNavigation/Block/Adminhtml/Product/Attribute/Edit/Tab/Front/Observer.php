<?php
/**
 * Product attribute edit form observer
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\LayeredNavigation\Block\Adminhtml\Product\Attribute\Edit\Tab\Front;

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
     * @return void
     */
    public function observe($event)
    {
        /** @var \Magento\Data\Form\AbstractForm $form */
        $form = $event->getForm();

        $fieldset = $form->getElement('front_fieldset');

        $fieldset->addField('is_filterable', 'select', array(
            'name' => 'is_filterable',
            'label' => __("Use In Layered Navigation"),
            'title' => __('Can be used only with catalog input type Dropdown, Multiple Select and Price'),
            'note' => __('Can be used only with catalog input type Dropdown, Multiple Select and Price'),
            'values' => array(
                array('value' => '0', 'label' => __('No')),
                array('value' => '1', 'label' => __('Filterable (with results)')),
                array('value' => '2', 'label' => __('Filterable (no results)')),
            ),
        ));

        $fieldset->addField('is_filterable_in_search', 'select', array(
            'name' => 'is_filterable_in_search',
            'label' => __("Use In Search Results Layered Navigation"),
            'title' => __('Can be used only with catalog input type Dropdown, Multiple Select and Price'),
            'note' => __('Can be used only with catalog input type Dropdown, Multiple Select and Price'),
            'values' => $this->optionList->toOptionArray(),
        ));
    }
}
