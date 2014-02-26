<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CustomerCustomAttributes
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Form Type Edit Form Block
 */
namespace Magento\CustomerCustomAttributes\Block\Adminhtml\Customer\Formtype\Edit;

class Form
    extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * @var \Magento\View\Design\Theme\LabelFactory
     */
    protected $_themeLabelFactory;

    /**
     * @var \Magento\Core\Model\System\Store
     */
    protected $_systemStore;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Registry $registry
     * @param \Magento\Data\FormFactory $formFactory
     * @param \Magento\View\Design\Theme\LabelFactory $themeLabelFactory
     * @param \Magento\Core\Model\System\Store $systemStore
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Registry $registry,
        \Magento\Data\FormFactory $formFactory,
        \Magento\View\Design\Theme\LabelFactory $themeLabelFactory,
        \Magento\Core\Model\System\Store $systemStore,
        array $data = array()
    ) {
        $this->_themeLabelFactory = $themeLabelFactory;
        $this->_systemStore = $systemStore;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Retrieve current form type instance
     *
     * @return \Magento\Eav\Model\Form\Type
     */
    protected function _getFormType()
    {
        return $this->_coreRegistry->registry('current_form_type');
    }

    /**
     * Prepare form before rendering HTML
     *
     * @return \Magento\CustomerCustomAttributes\Block\Adminhtml\Customer\Formtype\Edit\Form
     */
    protected function _prepareForm()
    {
        $editMode = $this->_coreRegistry->registry('edit_mode');
        if ($editMode == 'edit') {
            $saveUrl = $this->getUrl('adminhtml/*/save');
            $showNew = false;
        } else {
            $saveUrl = $this->getUrl('adminhtml/*/create');
            $showNew = true;
        }
        /** @var \Magento\Data\Form $form */
        $form = $this->_formFactory->create(array(
            'data' => array(
                'id'        => 'edit_form',
                'action'    => $saveUrl,
                'method'    => 'post',
            ))
        );

        if ($showNew) {
            $fieldset = $form->addFieldset('base_fieldset', array(
                'legend' => __('General Information'),
                'class'  => 'fieldset-wide'
            ));

            $options = $this->_getFormType()->getCollection()->toOptionArray();
            array_unshift($options, array(
                'label' => __('-- Please Select --'),
                'value' => ''
            ));
            $fieldset->addField('type_id', 'select', array(
                'name'      => 'type_id',
                'label'     => __('Based On'),
                'title'     => __('Based On'),
                'required'  => true,
                'values'    => $options
            ));

            $fieldset->addField('label', 'text', array(
                'name'      => 'label',
                'label'     => __('Form Label'),
                'title'     => __('Form Label'),
                'required'  => true,
            ));

            /** @var $label \Magento\View\Design\Theme\Label */
            $label = $this->_themeLabelFactory->create();
            $options = $label->getLabelsCollection();
            array_unshift($options, array(
                'label' => __('All Themes'),
                'value' => ''
            ));
            $fieldset->addField('theme', 'select', array(
                'name'      => 'theme',
                'label'     => __('For Theme'),
                'title'     => __('For Theme'),
                'values'    => $options
            ));

            $fieldset->addField('store_id', 'select', array(
                'name'      => 'store_id',
                'label'     => __('Store View'),
                'title'     => __('Store View'),
                'required'  => true,
                'values'    => $this->_systemStore->getStoreValuesForForm(false, true)
            ));

            $form->setValues($this->_getFormType()->getData());
        }

        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
