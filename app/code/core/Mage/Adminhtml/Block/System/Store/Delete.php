<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Store / store view / website delete form container
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_System_Store_Delete extends Mage_Adminhtml_Block_Widget_Form_Container
{

    /**
     * Class constructor
     *
     */
    protected function _construct()
    {
        $this->_objectId = 'item_id';
        $this->_mode = 'delete';
        $this->_controller = 'system_store';

        parent::_construct();

        $this->_removeButton('save');
        $this->_removeButton('reset');

        $this->_updateButton('delete', 'area', 'footer');
        $this->_updateButton('delete', 'onclick', 'editForm.submit();');

        $this->_addButton('cancel', array(
            'label'     => Mage::helper('Mage_Adminhtml_Helper_Data')->__('Cancel'),
            'onclick'   => 'setLocation(\'' . $this->getBackUrl() . '\')',
        ), 2, 100, 'footer');

    }

    /**
     * Get edit form container header text
     *
     * @return string
     */
    public function getHeaderText()
    {
        return Mage::helper('Mage_Adminhtml_Helper_Data')->__("Delete %s '%s'", $this->getStoreTypeTitle(),
            $this->escapeHtml($this->getChildBlock('form')->getDataObject()->getName()));
    }

    /**
     * Set store type title
     *
     * @param string $title
     * @return Mage_Adminhtml_Block_System_Store_Delete
     */
    public function setStoreTypeTitle($title)
    {
        $this->_updateButton('delete', 'label', Mage::helper('Mage_Adminhtml_Helper_Data')->__('Delete %s', $title));
        return $this->setData('store_type_title', $title);
    }

    /**
     * Set back URL for "Cancel" and "Back" buttons
     *
     * @param string $url
     * @return Mage_Adminhtml_Block_System_Store_Delete
     */
    public function setBackUrl($url)
    {
        $this->setData('back_url', $url);
        $this->_updateButton('cancel', 'onclick', "setLocation('" . $url . "')");
        $this->_updateButton('back', 'onclick', "setLocation('" . $url . "')");
        return $this;
    }

}
