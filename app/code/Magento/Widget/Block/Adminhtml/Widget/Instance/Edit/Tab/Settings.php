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
 * Widget Instance Settings tab block
 *
 * @category    Magento
 * @package     Magento_Widget
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Widget_Block_Adminhtml_Widget_Instance_Edit_Tab_Settings
    extends Magento_Backend_Block_Widget_Form_Generic
    implements Magento_Backend_Block_Widget_Tab_Interface
{
    /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry;

    /**
     * @var Magento_Core_Model_Theme_LabelFactory
     */
    protected $_themeLabelFactory;

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Core_Model_Theme_LabelFactory $themeLabelFactory
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_Registry $registry,
        Magento_Core_Model_Theme_LabelFactory $themeLabelFactory,
        array $data = array()
    ) {
        $this->_themeLabelFactory = $themeLabelFactory;
        parent::__construct($registry, $coreData, $context, $data);
    }

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
        return __('Settings');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Settings');
    }

    /**
     * Returns status flag about this tab can be showen or not
     *
     * @return true
     */
    public function canShowTab()
    {
        return !(bool)$this->getWidgetInstance()->isCompleteToCreate();
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
     * @return Magento_Widget_Model_Widget_Instance
     */
    public function getWidgetInstance()
    {
        return $this->_coreRegistry->registry('current_widget_instance');
    }

    /**
     * Prepare form before rendering HTML
     *
     * @return Magento_Widget_Block_Adminhtml_Widget_Instance_Edit_Tab_Settings
     */
    protected function _prepareForm()
    {
        /** @var Magento_Data_Form $form */
        $form = $this->_formFactory->create(array(
            'attributes' => array(
                'id' => 'edit_form',
                'action' => $this->getData('action'),
                'method' => 'post',
            ))
        );

        $fieldset = $form->addFieldset('base_fieldset',
            array('legend'=>__('Settings'))
        );

        $this->_addElementTypes($fieldset);

        $fieldset->addField('type', 'select', array(
            'name'     => 'type',
            'label'    => __('Type'),
            'title'    => __('Type'),
            'required' => true,
            'values'   => $this->getTypesOptionsArray()
        ));

        /** @var $label Magento_Core_Model_Theme_Label */
        $label = $this->_themeLabelFactory->create();
        $options = $label->getLabelsCollection(__('-- Please Select --'));
        $fieldset->addField('theme_id', 'select', array(
            'name'     => 'theme_id',
            'label'    => __('Design Theme'),
            'title'    => __('Design Theme'),
            'required' => true,
            'values'   => $options
        ));
        $continueButton = $this->getLayout()
            ->createBlock('Magento_Adminhtml_Block_Widget_Button')
            ->setData(array(
                'label'     => __('Continue'),
                'onclick'   => "setSettings('" . $this->getContinueUrl() . "', 'type', 'theme_id')",
                'class'     => 'save'
            ));
        $fieldset->addField('continue_button', 'note', array(
            'text' => $continueButton->toHtml(),
        ));

        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Return url for continue button
     *
     * @return string
     */
    public function getContinueUrl()
    {
        return $this->getUrl('*/*/*', array(
            '_current' => true,
            'type'     => '{{type}}',
            'theme_id' => '{{theme_id}}'
        ));
    }

    /**
     * Retrieve array (widget_type => widget_name) of available widgets
     *
     * @return array
     */
    public function getTypesOptionsArray()
    {
        $widgets = $this->getWidgetInstance()->getWidgetsOptionArray();
        array_unshift($widgets, array(
            'value' => '',
            'label' => __('-- Please Select --')
        ));
        return $widgets;
    }

    /**
     * User-defined widgets sorting by Name
     *
     * @param array $a
     * @param array $b
     * @return boolean
     */
    protected function _sortWidgets($a, $b)
    {
        return strcmp($a["label"], $b["label"]);
    }
}
