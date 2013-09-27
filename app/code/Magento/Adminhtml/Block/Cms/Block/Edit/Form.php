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
 * Adminhtml cms block edit form
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Block_Cms_Block_Edit_Form extends Magento_Backend_Block_Widget_Form_Generic
{
    /**
     * @var Magento_Cms_Model_Wysiwyg_Config
     */
    protected $_wysiwygConfig;

    /**
     * @var Magento_Core_Model_System_Store
     */
    protected $_systemStore;

    /**
     * @param Magento_Cms_Model_Wysiwyg_Config $wysiwygConfig
     * @param Magento_Core_Model_System_Store $systemStore
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Data_Form_Factory $formFactory
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_Cms_Model_Wysiwyg_Config $wysiwygConfig,
        Magento_Core_Model_System_Store $systemStore,
        Magento_Core_Model_Registry $registry,
        Magento_Data_Form_Factory $formFactory,
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_wysiwygConfig = $wysiwygConfig;
        $this->_systemStore = $systemStore;
        parent::__construct($registry, $formFactory, $coreData, $context, $data);
    }

    /**
     * Init form
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('block_form');
        $this->setTitle(__('Block Information'));
    }

    /**
     * Load Wysiwyg on demand and Prepare layout
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if ($this->_wysiwygConfig->isEnabled()) {
            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
        }
    }

    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('cms_block');

        /** @var Magento_Data_Form $form */
        $form   = $this->_formFactory->create(array(
            'attributes' => array(
                'id' => 'edit_form',
                'action' => $this->getData('action'),
                'method' => 'post',
            ))
        );

        $form->setHtmlIdPrefix('block_');

        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>__('General Information'), 'class' => 'fieldset-wide'));

        if ($model->getBlockId()) {
            $fieldset->addField('block_id', 'hidden', array(
                'name' => 'block_id',
            ));
        }

        $fieldset->addField('title', 'text', array(
            'name'      => 'title',
            'label'     => __('Block Title'),
            'title'     => __('Block Title'),
            'required'  => true,
        ));

        $fieldset->addField('identifier', 'text', array(
            'name'      => 'identifier',
            'label'     => __('Identifier'),
            'title'     => __('Identifier'),
            'required'  => true,
            'class'     => 'validate-xml-identifier',
        ));

        /**
         * Check is single store mode
         */
        if (!$this->_storeManager->isSingleStoreMode()) {
            $field =$fieldset->addField('store_id', 'multiselect', array(
                'name'      => 'stores[]',
                'label'     => __('Store View'),
                'title'     => __('Store View'),
                'required'  => true,
                'values'    => $this->_systemStore->getStoreValuesForForm(false, true),
            ));
            $renderer = $this->getLayout()
                ->createBlock('Magento_Backend_Block_Store_Switcher_Form_Renderer_Fieldset_Element');
            $field->setRenderer($renderer);
        } else {
            $fieldset->addField('store_id', 'hidden', array(
                'name'      => 'stores[]',
                'value'     => $this->_storeManager->getStore(true)->getId()
            ));
            $model->setStoreId($this->_storeManager->getStore(true)->getId());
        }

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
        if (!$model->getId()) {
            $model->setData('is_active', '1');
        }

        $fieldset->addField('content', 'editor', array(
            'name'      => 'content',
            'label'     => __('Content'),
            'title'     => __('Content'),
            'style'     => 'height:36em',
            'required'  => true,
            'config'    => $this->_wysiwygConfig->getConfig()
        ));

        $form->setValues($model->getData());
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
