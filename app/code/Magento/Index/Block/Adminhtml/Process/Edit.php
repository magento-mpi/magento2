<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Index
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Index_Block_Adminhtml_Process_Edit extends Magento_Adminhtml_Block_Widget_Form_Container
{

    protected function _construct()
    {
        $this->_objectId = 'process_id';
        $this->_controller = 'adminhtml_process';
        $this->_blockGroup = 'Magento_Index';

        parent::_construct();

        $this->_updateButton('save', 'label', __('Save Process'));
        if (Mage::registry('current_index_process')) {
            $this->_addButton('reindex', array(
                'label'     => __('Reindex Data'),
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
            return __("'%1' Index Process Information", $process->getIndexer()->getName());
        }
    }
}
