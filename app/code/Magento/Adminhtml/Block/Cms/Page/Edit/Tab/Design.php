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
namespace Magento\Adminhtml\Block\Cms\Page\Edit\Tab;

class Design
    extends \Magento\Backend\Block\Widget\Form\Generic
    implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
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
     * @return \Magento\Adminhtml\Block\Cms\Page\Edit\Tab\Design
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
            'values'   => \Mage::getSingleton('Magento\Page\Model\Source\Layout')->toOptionArray(),
            'disabled' => $isElementDisabled
        ));
        if (!$model->getId()) {
            $model->setRootTemplate(\Mage::getSingleton('Magento\Page\Model\Source\Layout')->getDefaultValue());
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

        $dateFormat = \Mage::app()->getLocale()->getDateFormat(\Magento\Core\Model\LocaleInterface::FORMAT_TYPE_SHORT);

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

        /** @var $label \Magento\Core\Model\Theme\Label */
        $label = \Mage::getModel('Magento\Core\Model\Theme\Label');
        $options = $label->getLabelsCollection(__('-- Please Select --'));
        $designFieldset->addField('custom_theme', 'select', array(
            'name'      => 'custom_theme',
            'label'     => __('Custom Theme'),
            'values'    => $options,
            'disabled'  => $isElementDisabled
        ));

        $designFieldset->addField('custom_root_template', 'select', array(
            'name'      => 'custom_root_template',
            'label'     => __('Custom Layout'),
            'values'    => \Mage::getSingleton('Magento\Page\Model\Source\Layout')->toOptionArray(true),
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
