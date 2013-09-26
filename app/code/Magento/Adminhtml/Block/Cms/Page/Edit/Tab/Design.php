<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 *
 * @SuppressWarnings(PHPMD.DepthOfInheritance)
 */
class Magento_Adminhtml_Block_Cms_Page_Edit_Tab_Design
    extends Magento_Backend_Block_Widget_Form_Generic
    implements Magento_Backend_Block_Widget_Tab_Interface
{
    /**
     * @var Magento_Core_Model_Theme_LabelFactory
     */
    protected $_labelFactory;

    /**
     * @var Magento_Page_Model_Source_Layout
     */
    protected $_pageLayout;

    /**
     * @param Magento_Page_Model_Source_Layout $pageLayout
     * @param Magento_Core_Model_Theme_LabelFactory $labelFactory
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Data_Form_Factory $formFactory
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_Page_Model_Source_Layout $pageLayout,
        Magento_Core_Model_Theme_LabelFactory $labelFactory,
        Magento_Core_Model_Registry $registry,
        Magento_Data_Form_Factory $formFactory,
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_labelFactory = $labelFactory;
        $this->_pageLayout = $pageLayout;
        parent::__construct($registry, $formFactory, $coreData, $context, $data);
    }

    /**
     * Prepare form tab configuration
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setShowGlobalIcon(true);
    }

    /**
     * Initialise form fields
     *
     * @return Magento_Adminhtml_Block_Cms_Page_Edit_Tab_Design
     */
    protected function _prepareForm()
    {
        /*
         * Checking if user have permissions to save information
         */
        $isElementDisabled = !$this->_isAllowedAction('Magento_Cms::save');

        /** @var Magento_Data_Form $form */
        $form   = $this->_formFactory->create(array(
            'attributes' => array(
                'html_id_prefix' => 'page_',
            ))
        );

        $model = $this->_coreRegistry->registry('cms_page');

        $layoutFieldset = $form->addFieldset('layout_fieldset', array(
            'legend' => __('Page Layout'),
            'class'  => 'fieldset-wide',
            'disabled'  => $isElementDisabled
        ));

        $layoutFieldset->addField('root_template', 'select', array(
            'name'     => 'root_template',
            'label'    => __('Layout'),
            'required' => true,
            'values'   => $this->_pageLayout->toOptionArray(),
            'disabled' => $isElementDisabled
        ));
        if (!$model->getId()) {
            $model->setRootTemplate($this->_pageLayout->getDefaultValue());
        }

        $layoutFieldset->addField('layout_update_xml', 'textarea', array(
            'name'      => 'layout_update_xml',
            'label'     => __('Layout Update XML'),
            'style'     => 'height:24em;',
            'disabled'  => $isElementDisabled
        ));

        $designFieldset = $form->addFieldset('design_fieldset', array(
            'legend' => __('Custom Design'),
            'class'  => 'fieldset-wide',
            'disabled'  => $isElementDisabled
        ));

        $dateFormat = $this->_locale->getDateFormat(Magento_Core_Model_LocaleInterface::FORMAT_TYPE_SHORT);

        $designFieldset->addField('custom_theme_from', 'date', array(
            'name'      => 'custom_theme_from',
            'label'     => __('Custom Design From'),
            'image'     => $this->getViewFileUrl('images/grid-cal.gif'),
            'date_format' => $dateFormat,
            'disabled'  => $isElementDisabled,
            'class'     => 'validate-date validate-date-range date-range-custom_theme-from'
        ));

        $designFieldset->addField('custom_theme_to', 'date', array(
            'name'      => 'custom_theme_to',
            'label'     => __('Custom Design To'),
            'image'     => $this->getViewFileUrl('images/grid-cal.gif'),
            'date_format' => $dateFormat,
            'disabled'  => $isElementDisabled,
            'class'     => 'validate-date validate-date-range date-range-custom_theme-to'
        ));

        $options = $this->_labelFactory->create()->getLabelsCollection(__('-- Please Select --'));
        $designFieldset->addField('custom_theme', 'select', array(
            'name'      => 'custom_theme',
            'label'     => __('Custom Theme'),
            'values'    => $options,
            'disabled'  => $isElementDisabled
        ));

        $designFieldset->addField('custom_root_template', 'select', array(
            'name'      => 'custom_root_template',
            'label'     => __('Custom Layout'),
            'values'    => $this->_pageLayout->toOptionArray(true),
            'disabled'  => $isElementDisabled
        ));

        $designFieldset->addField('custom_layout_update_xml', 'textarea', array(
            'name'      => 'custom_layout_update_xml',
            'label'     => __('Custom Layout Update XML'),
            'style'     => 'height:24em;',
            'disabled'  => $isElementDisabled
        ));

        $this->_eventManager->dispatch('adminhtml_cms_page_edit_tab_design_prepare_form', array('form' => $form));

        $form->setValues($model->getData());

        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Design');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Design');
    }

    /**
     * Returns status flag about this tab can be showen or not
     *
     * @return true
     */
    public function canShowTab()
    {
        return true;
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
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
}
