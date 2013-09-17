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
 * URL rewrites edit form
 *
 * @method Magento_Core_Model_Url_Rewrite getUrlRewrite()
 * @method Magento_Adminhtml_Block_Urlrewrite_Edit_Form setUrlRewrite(Magento_Core_Model_Url_Rewrite $model)
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 *
 * @SuppressWarnings(PHPMD.DepthOfInheritance)
 */
class Magento_Adminhtml_Block_Urlrewrite_Edit_Form extends Magento_Backend_Block_Widget_Form_Generic
{
    /**
     * @var array
     */
    protected $_sessionData = null;

    /**
     * @var array
     */
    protected $_allStores = null;

    /**
     * @var bool
     */
    protected $_requireStoresFilter = false;

    /**
     * @var array
     */
    protected $_formValues = array();

    /**
     * Adminhtml data
     *
     * @var Magento_Backend_Helper_Data
     */
    protected $_adminhtmlData = null;

    /**
     * @param Magento_Backend_Helper_Data $adminhtmlData
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Data_Form_Factory $formFactory
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_Backend_Helper_Data $adminhtmlData,
        Magento_Core_Model_Registry $registry,
        Magento_Data_Form_Factory $formFactory,
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_adminhtmlData = $adminhtmlData;
        parent::__construct($registry, $formFactory, $coreData, $context, $data);
    }

    /**
     * Set form id and title
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('urlrewrite_form');
        $this->setTitle(__('Block Information'));
    }

    /**
     * Initialize form values
     * Set form data either from model values or from session
     *
     * @return Magento_Adminhtml_Block_Urlrewrite_Edit_Form
     */
    protected function _initFormValues()
    {
        $model = $this->_getModel();
        $this->_formValues = array(
            'store_id'     => $model->getStoreId(),
            'id_path'      => $model->getIdPath(),
            'request_path' => $model->getRequestPath(),
            'target_path'  => $model->getTargetPath(),
            'options'      => $model->getOptions(),
            'description'  => $model->getDescription(),
        );

        $sessionData = $this->_getSessionData();
        if ($sessionData) {
            foreach (array_keys($this->_formValues) as $key) {
                if (isset($sessionData[$key])) {
                    $this->_formValues[$key] = $sessionData[$key];
                }
            }
        }

        return $this;
    }

    /**
     * Prepare the form layout
     *
     * @return Magento_Adminhtml_Block_Urlrewrite_Edit_Form
     */
    protected function _prepareForm()
    {
        $this->_initFormValues();

        // Prepare form
        /** @var Magento_Data_Form $form */
        $form = $this->_formFactory->create(array(
            'attributes' => array(
                'id'            => 'edit_form',
                'use_container' => true,
                'method'        => 'post',
            ))
        );

        $fieldset = $form->addFieldset('base_fieldset', array(
            'legend' => __('URL Rewrite Information')
        ));

        /** @var $typesModel Magento_Core_Model_Source_Urlrewrite_Types */
        $typesModel = Mage::getModel('Magento_Core_Model_Source_Urlrewrite_Types');
        $fieldset->addField('is_system', 'select', array(
            'label'    => __('Type'),
            'title'    => __('Type'),
            'name'     => 'is_system',
            'required' => true,
            'options'  => $typesModel->getAllOptions(),
            'disabled' => true,
            'value'    => $this->_getModel()->getIsSystem()
        ));

        $fieldset->addField('id_path', 'text', array(
            'label'    => __('ID Path'),
            'title'    => __('ID Path'),
            'name'     => 'id_path',
            'required' => true,
            'disabled' => false,
            'value'    => $this->_formValues['id_path']
        ));

        $fieldset->addField('request_path', 'text', array(
            'label'    => __('Request Path'),
            'title'    => __('Request Path'),
            'name'     => 'request_path',
            'required' => true,
            'value'    => $this->_formValues['request_path']
        ));

        $fieldset->addField('target_path', 'text', array(
            'label'    => __('Target Path'),
            'title'    => __('Target Path'),
            'name'     => 'target_path',
            'required' => true,
            'disabled' => false,
            'value'    => $this->_formValues['target_path'],
        ));

        /** @var $optionsModel Magento_Core_Model_Source_Urlrewrite_Options */
        $optionsModel = Mage::getModel('Magento_Core_Model_Source_Urlrewrite_Options');
        $fieldset->addField('options', 'select', array(
            'label'   => __('Redirect'),
            'title'   => __('Redirect'),
            'name'    => 'options',
            'options' => $optionsModel->getAllOptions(),
            'value'   => $this->_formValues['options']
        ));

        $fieldset->addField('description', 'textarea', array(
            'label' => __('Description'),
            'title' => __('Description'),
            'name'  => 'description',
            'cols'  => 20,
            'rows'  => 5,
            'value' => $this->_formValues['description'],
            'wrap'  => 'soft'
        ));

        $this->_prepareStoreElement($fieldset);

        $this->setForm($form);
        $this->_formPostInit($form);

        return parent::_prepareForm();
    }

    /**
     * Prepare store element
     *
     * @param Magento_Data_Form_Element_Fieldset $fieldset
     */
    protected function _prepareStoreElement($fieldset)
    {
        // get store switcher or a hidden field with it's id
        if (Mage::app()->isSingleStoreMode()) {
            $fieldset->addField('store_id', 'hidden', array(
                'name'  => 'store_id',
                'value' => Mage::app()->getStore(true)->getId()
            ), 'id_path');
        } else {
            /** @var $renderer Magento_Backend_Block_Store_Switcher_Form_Renderer_Fieldset_Element */
            $renderer = $this->getLayout()
                ->createBlock('Magento_Backend_Block_Store_Switcher_Form_Renderer_Fieldset_Element');

            $storeElement = $fieldset->addField('store_id', 'select', array(
                'label'    => __('Store'),
                'title'    => __('Store'),
                'name'     => 'store_id',
                'required' => true,
                'values'   => $this->_getRestrictedStoresList(),
                'disabled' => $this->_getModel()->getIsSystem(),
                'value'    => $this->_formValues['store_id'],
            ), 'id_path');
            $storeElement->setRenderer($renderer);
        }
    }

    /**
     * Form post init
     *
     * @param Magento_Data_Form $form
     * @return Magento_Adminhtml_Block_Urlrewrite_Edit_Form
     */
    protected function _formPostInit($form)
    {
        $form->setAction($this->_adminhtmlData->getUrl('*/*/save', array(
            'id' => $this->_getModel()->getId()
        )));
        return $this;
    }

    /**
     * Get session data
     *
     * @return array
     */
    protected function _getSessionData()
    {
        if (is_null($this->_sessionData)) {
            $this->_sessionData = Mage::getModel('Magento_Adminhtml_Model_Session')->getData('urlrewrite_data', true);
        }
        return $this->_sessionData;
    }

    /**
     * Get URL rewrite model instance
     *
     * @return Magento_Core_Model_Url_Rewrite
     */
    protected function _getModel()
    {
        if (!$this->hasData('url_rewrite')) {
            $this->setUrlRewrite(Mage::getModel('Magento_Core_Model_Url_Rewrite'));
        }
        return $this->getUrlRewrite();
    }

    /**
     * Get request stores
     *
     * @return array
     */
    protected function _getAllStores()
    {
        if (is_null($this->_allStores)) {
            $this->_allStores = Mage::getSingleton('Magento_Core_Model_System_Store')->getStoreValuesForForm();
        }

        return $this->_allStores;
    }

    /**
     * Get entity stores
     *
     * @return array
     */
    protected function _getEntityStores()
    {
        return $this->_getAllStores();
    }

    /**
     * Get restricted stores list
     * Stores should be filtered only if custom entity is specified.
     * If we use custom rewrite, all stores are accepted.
     *
     * @return array
     */
    protected function _getRestrictedStoresList()
    {
        $stores = $this->_getAllStores();
        $entityStores = $this->_getEntityStores();
        $stores = $this->_getStoresListRestrictedByEntityStores($stores, $entityStores);

        return $stores;
    }

    /**
     * Get stores list restricted by entity stores
     *
     * @param array $stores
     * @param array $entityStores
     * @return array
     */
    private function _getStoresListRestrictedByEntityStores(array $stores, array $entityStores)
    {
        if ($this->_requireStoresFilter) {
            foreach ($stores as $i => $store) {
                if (isset($store['value']) && $store['value']) {
                    $found = false;
                    foreach ($store['value'] as $k => $v) {
                        if (isset($v['value']) && in_array($v['value'], $entityStores)) {
                            $found = true;
                        } else {
                            unset($stores[$i]['value'][$k]);
                        }
                    }
                    if (!$found) {
                        unset($stores[$i]);
                    }
                }
            }
        }

        return $stores;
    }
}
