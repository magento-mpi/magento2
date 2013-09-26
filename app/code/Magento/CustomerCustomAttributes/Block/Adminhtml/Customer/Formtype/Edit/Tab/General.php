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
 * Form Type Edit General Tab Block
 */
namespace Magento\CustomerCustomAttributes\Block\Adminhtml\Customer\Formtype\Edit\Tab;

class General
    extends \Magento\Backend\Block\Widget\Form\Generic
    implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var \Magento\Core\Model\Theme\LabelFactory
     */
    protected $_themeLabelFactory;

    /**
     * @var \Magento\Core\Model\System\Store
     */
    protected $_systemStore;

    /**
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Data\Form\Factory $formFactory
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Model\Theme\LabelFactory $themeLabelFactory
     * @param \Magento\Core\Model\System\Store $systemStore
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Model\Registry $registry,
        \Magento\Data\Form\Factory $formFactory,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Model\Theme\LabelFactory $themeLabelFactory,
        \Magento\Core\Model\System\Store $systemStore,
        array $data = array()
    ) {
        $this->_themeLabelFactory = $themeLabelFactory;
        $this->_systemStore = $systemStore;
        parent::__construct($registry, $formFactory, $coreData, $context, $data);
    }

    /**
     * Initialize Edit Form
     */
    protected function _construct()
    {
        $this->setDestElementId('edit_form');
        $this->setShowGlobalIcon(false);
        parent::_construct();
    }

    /**
     * Prepare form before rendering HTML
     *
     * @return \Magento\CustomerCustomAttributes\Block\Adminhtml\Customer\Formtype\Edit\Tab\General
     */
    protected function _prepareForm()
    {
        /* @var $model \Magento\Eav\Model\Form\Type */
        $model      = $this->_coreRegistry->registry('current_form_type');

        /** @var \Magento\Data\Form $form */
        $form       = $this->_formFactory->create();
        $fieldset   = $form->addFieldset('general_fieldset', array(
            'legend'    => __('General Information')
        ));

        $fieldset->addField('continue_edit', 'hidden', array(
            'name'      => 'continue_edit',
            'value'     => 0
        ));
        $fieldset->addField('type_id', 'hidden', array(
            'name'      => 'type_id',
            'value'     => $model->getId()
        ));

        $fieldset->addField('form_type_data', 'hidden', array(
            'name'      => 'form_type_data'
        ));

        $fieldset->addField('code', 'text', array(
            'name'      => 'code',
            'label'     => __('Form Code'),
            'title'     => __('Form Code'),
            'required'  => true,
            'class'     => 'validate-code',
            'disabled'  => true,
            'value'     => $model->getCode()
        ));

        $fieldset->addField('label', 'text', array(
            'name'      => 'label',
            'label'     => __('Form Title'),
            'title'     => __('Form Title'),
            'required'  => true,
            'value'     => $model->getLabel()
        ));

        /** @var $label \Magento\Core\Model\Theme\Label */
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
            'values'    => $options,
            'value'     => $model->getTheme(),
            'disabled'  => true
        ));

        $fieldset->addField('store_id', 'select', array(
            'name'      => 'store_id',
            'label'     => __('Store View'),
            'title'     => __('Store View'),
            'values'    => $this->_systemStore->getStoreValuesForForm(false, true),
            'value'     => $model->getStoreId(),
            'disabled'  => true
        ));

        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Retrieve Tab label
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('General');
    }

    /**
     * Retrieve Tab title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('General');
    }

    /**
     * Check is can show tab
     *
     * @return bool
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Check tab is hidden
     *
     * @return bool
     */
    public function isHidden()
    {
        return false;
    }
}
