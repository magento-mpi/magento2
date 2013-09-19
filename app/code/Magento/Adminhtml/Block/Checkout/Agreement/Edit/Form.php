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
 * Adminhtml Tax Rule Edit Form
 */

namespace Magento\Adminhtml\Block\Checkout\Agreement\Edit;

class Form extends \Magento\Backend\Block\Widget\Form
{
    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Data\Form\Factory
     */
    protected $_formFactory;

    /**
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Data\Form\Factory $formFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Helper\Data $coreData,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Model\Registry $registry,
        \Magento\Data\Form\Factory $formFactory,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        $this->_formFactory = $formFactory;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Init class
     *
     */
    protected function _construct()
    {
        parent::_construct();

        $this->setId('checkoutAgreementForm');
        $this->setTitle(__('Terms and Conditions Information'));
    }

    /**
     *
     * return \Magento\Adminhtml\Block\Widget\Form
     */
    protected function _prepareForm()
    {
        $model  = $this->_coreRegistry->registry('checkout_agreement');
        /** @var \Magento\Data\Form $form */
        $form   = $this->_formFactory->create(array(
            'attributes' => array(
                'id'        => 'edit_form',
                'action'    => $this->getData('action'),
                'method'    => 'post',
            ))
        );

        $fieldset   = $form->addFieldset('base_fieldset', array(
            'legend'    => __('Terms and Conditions Information'),
            'class'     => 'fieldset-wide',
        ));

        if ($model->getId()) {
            $fieldset->addField('agreement_id', 'hidden', array(
                'name' => 'agreement_id',
            ));
        }
        $fieldset->addField('name', 'text', array(
            'name'      => 'name',
            'label'     => __('Condition Name'),
            'title'     => __('Condition Name'),
            'required'  => true,
        ));

        $fieldset->addField('is_active', 'select', array(
            'label'     => __('Status'),
            'title'     => __('Status'),
            'name'      => 'is_active',
            'required'  => true,
            'options'   => array(
                '1' => __('Enabled'),
                '0' => __('Disabled'),
            ),
        ));

        $fieldset->addField('is_html', 'select', array(
            'label'     => __('Show Content as'),
            'title'     => __('Show Content as'),
            'name'      => 'is_html',
            'required'  => true,
            'options'   => array(
                0 => __('Text'),
                1 => __('HTML'),
            ),
        ));

        if (!\Mage::app()->isSingleStoreMode()) {
            $field = $fieldset->addField('store_id', 'multiselect', array(
                'name'      => 'stores[]',
                'label'     => __('Store View'),
                'title'     => __('Store View'),
                'required'  => true,
                'values'    => \Mage::getSingleton('Magento\Core\Model\System\Store')
                    ->getStoreValuesForForm(false, true),
            ));
            $renderer = $this->getLayout()
                ->createBlock('Magento\Backend\Block\Store\Switcher\Form\Renderer\Fieldset\Element');
            $field->setRenderer($renderer);
        } else {
            $fieldset->addField('store_id', 'hidden', array(
                'name'      => 'stores[]',
                'value'     => \Mage::app()->getStore(true)->getId()
            ));
            $model->setStoreId(\Mage::app()->getStore(true)->getId());
        }

        $fieldset->addField('checkbox_text', 'editor', array(
            'name'      => 'checkbox_text',
            'label'     => __('Checkbox Text'),
            'title'     => __('Checkbox Text'),
            'rows'      => '5',
            'cols'      => '30',
            'wysiwyg'   => false,
            'required'  => true,
        ));

        $fieldset->addField('content', 'editor', array(
            'name'      => 'content',
            'label'     => __('Content'),
            'title'     => __('Content'),
            'style'     => 'height:24em;',
            'wysiwyg'   => false,
            'required'  => true,
        ));

        $fieldset->addField('content_height', 'text', array(
            'name'      => 'content_height',
            'label'     => __('Content Height (css)'),
            'title'     => __('Content Height'),
            'maxlength' => 25,
            'class'     => 'validate-css-length',
        ));

        $form->setValues($model->getData());
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
