<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Connect
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Block for Dependencies
 *
 * @category    Magento
 * @package     Magento_Connect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Connect_Block_Adminhtml_Extension_Custom_Edit_Tab_Depends
    extends Magento_Connect_Block_Adminhtml_Extension_Custom_Edit_Tab_Abstract
{

    /**
     * Prepare Dependencies Form before rendering HTML
     *
     * @return Magento_Connect_Block_Adminhtml_Extension_Custom_Edit_Tab_Package
     */
    protected function _prepareForm()
    {
        parent::_prepareForm();

        /** @var Magento_Data_Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('_depends');

        $fieldset = $form->addFieldset('depends_php_fieldset', array(
            'legend'    => __('PHP Version')
        ));

        $fieldset->addField('depends_php_min', 'text', array(
            'name'      => 'depends_php_min',
            'label'     => __('Minimum'),
            'required'  => true,
            'value'     => PHP_MAJOR_VERSION . '.' . PHP_MINOR_VERSION . '.' . PHP_RELEASE_VERSION,
        ));

        $fieldset->addField('depends_php_max', 'text', array(
            'name'      => 'depends_php_max',
            'label'     => __('Maximum'),
            'required'  => true,
            'value'     => PHP_MAJOR_VERSION . '.' . (PHP_MINOR_VERSION + 1) . '.0',
        ));

        $form->setValues($this->getData());
        $this->setForm($form);

        return $this;
    }

    /**
     * Retrieve list of loaded PHP extensions
     *
     * @return array
     */
    public function getExtensions()
    {
        $extensions = array();
        foreach (get_loaded_extensions() as $ext) {
            $extensions[$ext] = $ext;
        }
        asort($extensions, SORT_STRING);
        return $extensions;
    }

    /**
     * Get Tab Label
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Dependencies');
    }

    /**
     * Get Tab Title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Dependencies');
    }
}
