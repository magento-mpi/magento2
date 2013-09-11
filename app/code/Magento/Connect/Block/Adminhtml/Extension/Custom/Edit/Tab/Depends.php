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
namespace Magento\Connect\Block\Adminhtml\Extension\Custom\Edit\Tab;

class Depends
    extends \Magento\Connect\Block\Adminhtml\Extension\Custom\Edit\Tab\AbstractTab
{

    /**
     * Prepare Dependencies Form before rendering HTML
     *
     * @return \Magento\Connect\Block\Adminhtml\Extension\Custom\Edit\Tab\Package
     */
    protected function _prepareForm()
    {
        parent::_prepareForm();

        $form = new \Magento\Data\Form();
        $form->setHtmlIdPrefix('_depends');

        $fieldset = $form->addFieldset('depends_php_fieldset', array(
            'legend'    => __('PHP Version')
        ));

        $fieldset->addField('depends_php_min', 'text', array(
            'name'      => 'depends_php_min',
            'label'     => __('Minimum'),
            'required'  => true,
            'value'     => '5.2.0',
        ));

        $fieldset->addField('depends_php_max', 'text', array(
            'name'      => 'depends_php_max',
            'label'     => __('Maximum'),
            'required'  => true,
            'value'     => '5.2.20',
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
