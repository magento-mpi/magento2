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
    /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_Registry $registry,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($coreData, $context, $data);
    }

    protected function _construct()
    {
        $this->_objectId = 'process_id';
        $this->_controller = 'adminhtml_process';
        $this->_blockGroup = 'Magento_Index';

        parent::_construct();

        $this->_updateButton('save', 'label', __('Save Process'));
        if ($this->_coreRegistry->registry('current_index_process')) {
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
            'process' => $this->_coreRegistry->registry('current_index_process')->getId()
        ));
    }

    /**
     * Retrieve text for header element depending on loaded page
     *
     * @return string
     */
    public function getHeaderText()
    {
        $process = $this->_coreRegistry->registry('current_index_process');
        if ($process && $process->getId()) {
            return __("'%1' Index Process Information", $process->getIndexer()->getName());
        }
    }
}
