<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Widget
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Widget Instance Main tab block
 *
 * @category    Magento
 * @package     Magento_Widget
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Widget\Block\Adminhtml\Widget\Instance\Edit\Tab;

class Main
    extends \Magento\Adminhtml\Block\Widget\Form
    implements \Magento\Adminhtml\Block\Widget\Tab\TabInterface
{
    /**
     * Internal constructor
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setActive(true);
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Frontend Properties');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Frontend Properties');
    }

    /**
     * Returns status flag about this tab can be showen or not
     *
     * @return true
     */
    public function canShowTab()
    {
        return $this->getWidgetInstance()->isCompleteToCreate();
    }

    /**
     * Returns status flag about this tab hidden or not
     *
     * @return true
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Getter
     *
     * @return Widget_Model_Widget_Instance
     */
    public function getWidgetInstance()
    {
        return \Mage::registry('current_widget_instance');
    }

    /**
     * Prepare form before rendering HTML
     *
     * @return \Magento\Widget\Block\Adminhtml\Widget\Instance\Edit\Tab\Main
     */
    protected function _prepareForm()
    {
        $widgetInstance = $this->getWidgetInstance();
        $form = new \Magento\Data\Form(array(
            'id' => 'edit_form',
            'action' => $this->getData('action'),
            'method' => 'post'
        ));

        $fieldset = $form->addFieldset('base_fieldset',
            array('legend' => __('Frontend Properties'))
        );

        if ($widgetInstance->getId()) {
            $fieldset->addField('instance_id', 'hidden', array(
                'name' => 'isntance_id',
            ));
        }

        $this->_addElementTypes($fieldset);

        $fieldset->addField('instance_type', 'select', array(
            'name'  => 'instance_type',
            'label' => __('Type'),
            'title' => __('Type'),
            'class' => '',
            'values' => $this->getTypesOptionsArray(),
            'disabled' => true
        ));

        /** @var $label \Magento\Core\Model\Theme\Label */
        $label = \Mage::getModel('Magento\Core\Model\Theme\Label');
        $options = $label->getLabelsCollection(__('-- Please Select --'));
        $fieldset->addField('theme_id', 'select', array(
            'name'  => 'theme_id',
            'label' => __('Design Package/Theme'),
            'title' => __('Design Package/Theme'),
            'required' => false,
            'values'   => $options,
            'disabled' => true
        ));

        $fieldset->addField('title', 'text', array(
            'name'  => 'title',
            'label' => __('Widget Instance Title'),
            'title' => __('Widget Instance Title'),
            'class' => '',
            'required' => true,
        ));

        if (!\Mage::app()->isSingleStoreMode()) {
            $field = $fieldset->addField('store_ids', 'multiselect', array(
                'name'      => 'store_ids[]',
                'label'     => __('Assign to Store Views'),
                'title'     => __('Assign to Store Views'),
                'required'  => true,
                'values'    => \Mage::getSingleton('Magento\Core\Model\System\Store')->getStoreValuesForForm(false, true),
            ));
            $renderer = $this->getLayout()
                ->createBlock('Magento\Backend\Block\Store\Switcher\Form\Renderer\Fieldset\Element');
            $field->setRenderer($renderer);
        }

        $fieldset->addField('sort_order', 'text', array(
            'name'  => 'sort_order',
            'label' => __('Sort Order'),
            'title' => __('Sort Order'),
            'class' => '',
            'required' => false,
            'note' => __('Sort Order of widget instances in the same container')
        ));

        /* @var $layoutBlock \Magento\Widget\Block\Adminhtml\Widget\Instance\Edit\Tab\Main\Layout */
        $layoutBlock = $this->getLayout()
            ->createBlock('Magento\Widget\Block\Adminhtml\Widget\Instance\Edit\Tab\Main\Layout')
            ->setWidgetInstance($widgetInstance);
        $fieldset = $form->addFieldset('layout_updates_fieldset',
            array('legend' => __('Layout Updates'))
        );
        $fieldset->addField('layout_updates', 'note', array(
        ));
        $form->getElement('layout_updates_fieldset')->setRenderer($layoutBlock);
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Retrieve array (widget_type => widget_name) of available widgets
     *
     * @return array
     */
    public function getTypesOptionsArray()
    {
        return $this->getWidgetInstance()->getWidgetsOptionArray();
    }

    /**
     * Initialize form fileds values
     *
     * @return \Magento\Widget\Block\Adminhtml\Widget\Instance\Edit\Tab\Main
     */
    protected function _initFormValues()
    {
        $this->getForm()->addValues($this->getWidgetInstance()->getData());
        return parent::_initFormValues();
    }
}
