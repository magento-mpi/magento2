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
 * Theme editor container
 */
class Mage_Adminhtml_Block_System_Design_Theme_Edit extends Mage_Backend_Block_Widget
{
    /**
     * Class constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('theme_edit');
    }

    /**
     * Create controls
     *
     * @return Mage_Backend_Block_Widget_Form|void
     */
    protected function _prepareLayout()
    {
        $this->setChild('back_button',
            $this->getLayout()->createBlock('Mage_Backend_Block_Widget_Button')
                ->setData(array(
                'label'   => Mage::helper('Mage_Core_Helper_Data')->__('Back'),
                'onclick' => 'setLocation(\''.$this->getUrl('*/*/').'\')',
                'class'   => 'back'
            ))
        );

        $this->setChild('save_button',
            $this->getLayout()->createBlock('Mage_Backend_Block_Widget_Button')
                ->setData(array(
                'label'   => Mage::helper('Mage_Core_Helper_Data')->__('Save'),
                'onclick' => 'themeForm.submit()',
                'class'   => 'save'
            ))
        );

        $this->setChild('delete_button',
            $this->getLayout()->createBlock('Mage_Backend_Block_Widget_Button')
                ->setData(array(
                'label'   => Mage::helper('Mage_Core_Helper_Data')->__('Delete'),
                'onclick' => 'confirmSetLocation(\''.Mage::helper('Mage_Core_Helper_Data')->__('Are you sure?').'\', \''.$this->getDeleteUrl().'\')',
                'class'   => 'delete'
            ))
        );
        return parent::_prepareLayout();
    }

    /**
     * Get theme id
     *
     * @return int
     */
    public function getThemeId()
    {
        return Mage::registry('theme')->getId();
    }

    /**
     * Prepare delete url
     *
     * @return string
     */
    public function getDeleteUrl()
    {
        return $this->getUrl('*/*/delete', array('_current'=>true));
    }

    /**
     * Prepare save url
     *
     * @return string
     */
    public function getSaveUrl()
    {
        return $this->getUrl('*/*/save', array('_current'=>true));
    }

    /**
     * Prepare header for container
     *
     * @return string
     */
    public function getHeader()
    {
        if (Mage::registry('theme')->getId()) {
            $header = Mage::helper('Mage_Core_Helper_Data')->__('Edit Theme');
        } else {
            $header = Mage::helper('Mage_Core_Helper_Data')->__('New Theme');
        }
        return $header;
    }
}
