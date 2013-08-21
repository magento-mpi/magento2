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
 * Extension edit page
 *
 * @category    Magento
 * @package     Magento_Connect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Connect_Block_Adminhtml_Extension_Custom_Edit extends Magento_Adminhtml_Block_Widget_Form_Container
{
    /**
     * Constructor
     *
     * Initializes edit form container, adds necessary buttons
     */
    protected function _construct()
    {
        $this->_objectId    = 'id';
        $this->_blockGroup  = 'Magento_Connect';
        $this->_controller  = 'adminhtml_extension_custom';

        parent::_construct();

        $this->_removeButton('back');
        $this->_updateButton('reset', 'onclick', "resetPackage()");

        $this->_addButton('create', array(
            'label'     => __('Save Data and Create Package'),
            'class'     => 'save',
            'onclick'   => "createPackage()",
            'data_attribute'  => array(
                'mage-init' => array(
                    'button' => array('event' => 'save', 'target' => '#edit_form'),
                ),
            ),
        ));
        $this->_addButton('save_as', array(
            'label'     => __('Save As...'),
            'title'     => __('Save package with custom package file name'),
            'onclick'   => 'saveAsPackage(event)',
            'data_attribute'  => array(
                'mage-init' => array(
                    'button' => array('event' => 'save', 'target' => '#edit_form'),
                ),
            ),
        ));
    }

    /**
    * Get header of page
    *
    * @return string
    */
    public function getHeaderText()
    {
        return __('New Extension');
    }

    /*
     * Get form submit URL
     *
     * @return string
     */
    public function getFormActionUrl()
    {
        return $this->getUrl('*/*/save');
    }
}
