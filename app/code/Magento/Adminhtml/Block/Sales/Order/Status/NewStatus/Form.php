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
 * Create order status form
 */
namespace Magento\Adminhtml\Block\Sales\Order\Status\NewStatus;

class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    protected function _construct()
    {
        parent::_construct();
        $this->setId('new_order_status');
    }

    /**
     * Prepare form fields and structure
     *
     * @return \Magento\Adminhtml\Block\Widget\Form
     */
    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('current_status');

        /** @var \Magento\Data\Form $form */
        $form = $this->_formFactory->create(array(
            'attributes' => array(
                'id' => 'edit_form',
                'action' => $this->getData('action'),
                'method' => 'post',
            ))
        );

        $fieldset = $form->addFieldset('base_fieldset', array(
            'legend' => __('Order Status Information')
        ));

        $fieldset->addField('is_new', 'hidden', array('name' => 'is_new', 'value' => 1));

        $fieldset->addField('status', 'text',
            array(
                'name' => 'status',
                'label' => __('Status Code'),
                'class' => 'required-entry validate-code',
                'required' => true,
            )
        );

        $fieldset->addField('label', 'text', array(
            'name' => 'label',
            'label' => __('Status Label'),
            'class' => 'required-entry',
            'required' => true,
        ));

        if (!\Mage::app()->isSingleStoreMode()) {
            $this->_addStoresFieldset($model, $form);
        }

        if ($model) {
            $form->addValues($model->getData());
        }
        $form->setAction($this->getUrl('*/sales_order_status/save'));
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Add Fieldset with Store labels
     *
     * @param \Magento\Sales\Model\Order\Status $model
     * @param \Magento\Data\Form $form
     */
    protected function _addStoresFieldset($model, $form)
    {
        $labels = $model ? $model->getStoreLabels() : array();
        $fieldset = $form->addFieldset('store_labels_fieldset', array(
            'legend' => __('Store View Specific Labels'),
            'class' => 'store-scope',
        ));
        $renderer = $this->getLayout()->createBlock('Magento\Backend\Block\Store\Switcher\Form\Renderer\Fieldset');
        $fieldset->setRenderer($renderer);

        foreach (\Mage::app()->getWebsites() as $website) {
            $fieldset->addField("w_{$website->getId()}_label", 'note', array(
                'label' => $website->getName(),
                'fieldset_html_class' => 'website',
            ));
            foreach ($website->getGroups() as $group) {
                $stores = $group->getStores();
                if (count($stores) == 0) {
                    continue;
                }
                $fieldset->addField("sg_{$group->getId()}_label", 'note', array(
                    'label' => $group->getName(),
                    'fieldset_html_class' => 'store-group',
                ));
                foreach ($stores as $store) {
                    $fieldset->addField("store_label_{$store->getId()}", 'text', array(
                        'name' => 'store_labels[' . $store->getId() . ']',
                        'required' => false,
                        'label' => $store->getName(),
                        'value' => isset($labels[$store->getId()]) ? $labels[$store->getId()] : '',
                        'fieldset_html_class' => 'store',
                    ));
                }
            }
        }
    }
}
