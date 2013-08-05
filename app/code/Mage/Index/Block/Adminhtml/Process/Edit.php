<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Index
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Index_Block_Adminhtml_Process_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{

    protected function _construct()
    {
        $this->_objectId = 'process_id';
        $this->_controller = 'adminhtml_process';
        $this->_blockGroup = 'Mage_Index';

        parent::_construct();

        $this->_updateButton('save', 'label', Mage::helper('Mage_Cms_Helper_Data')->__('Save Process'));
        if (Mage::registry('current_index_process')) {
            $this->_addButton('reindex', array(
                'label'     => Mage::helper('Mage_Index_Helper_Data')->__('Reindex Data'),
                'onclick'   => "setLocation('{$this->getRunUrl()}')"
            ));
        }
        $this->_removeButton('reset');
        $this->_removeButton('delete');
    }

    /**
     * Get back button url
     *
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('adminhtml/process/list');
    }

    /**
     * Get process reindex action url
     *
     * @return string
     */
    public function getRunUrl()
    {
        return $this->getUrl('adminhtml/process/reindexProcess', array(
            'process' => Mage::registry('current_index_process')->getId()
        ));
    }

    /**
     * Retrieve text for header element depending on loaded page
     *
     * @return string
     */
    public function getHeaderText()
    {
        $process = Mage::registry('current_index_process');
        if ($process && $process->getId()) {
            return Mage::helper('Mage_Index_Helper_Data')->__("'%1' Index Process Information", $process->getIndexer()->getName());
        }
    }
}
