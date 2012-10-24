<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Theme
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Theme editor container
 */
class Mage_Theme_Block_Adminhtml_System_Design_Theme_Edit extends Mage_Backend_Block_Widget_Form_Container
{
    /**
     * Prepare layout
     *
     * @return Mage_Core_Block_Abstract
     */
    protected function _prepareLayout()
    {
        $this->_blockGroup = 'Mage_Theme';
        $this->_controller = 'Adminhtml_System_Design_Theme';
        $this->setId('theme_edit');

        $this->_addButton('save_and_continue', array(
            'label'   => $this->__('Save and Continue Edit'),
            'onclick' => "editForm.submit($('edit_form').action+'back/edit/');",
            'class'   => 'save',
        ), 1);

        /** @var $theme Mage_Core_Model_Theme */
        $theme = Mage::registry('current_theme');

        if ($theme->hasChildThemes()) {
            $onClick = 'deleteConfirm(\'' . $this->__('Theme contains child themes. Their parent will be modified.')
                . ' ' . $this->__('Are you sure you want to do this?')
                . '\', \'' . $this->getUrl('*/*/delete', array('id' => $theme->getId())) . '\')';

            $this->_updateButton('delete', 'onclick', $onClick);
        }

        if ($theme->getId() && !$theme->isDeletable()) {
            $this->_removeButton('delete');
        }

        return parent::_prepareLayout();
    }

    /**
     * Prepare header for container
     *
     * @return string
     */
    public function getHeaderText()
    {
        if (Mage::registry('current_theme')->getId()) {
            $header = $this->__('Theme: %s', Mage::registry('current_theme')->getThemeTitle());
        } else {
            $header = $this->__('New Theme');
        }
        return $header;
    }
}
